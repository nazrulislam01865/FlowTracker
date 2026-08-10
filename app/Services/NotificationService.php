<?php

namespace App\Services;

use App\Jobs\DeliverRealtimeNotification;
use App\Models\FlowJob;
use App\Models\FlowNotification;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function visibleQuery(User $user): Builder
    {
        $query = FlowNotification::query()->where('user_id', $user->id);

        // Administrator copies are only visible while the account is actually an
        // Admin/Super Admin. This keeps the rule correct even if a user's role is
        // changed later: normal users always see only mentions addressed to them.
        if (! app(AccessControlService::class)->isAdministrator($user)) {
            $query->where('type', '!=', 'mention_admin');
        }

        return $query;
    }

    public function list(User $user)
    {
        return $this->visibleQuery($user)
            ->with(['job','task','inquiry','inquiryTask'])
            ->latest()
            ->get();
    }

    public function paginate(User $user, int $perPage = 30)
    {
        return $this->visibleQuery($user)
            ->with(['job','task','inquiry','inquiryTask'])
            ->latest()
            ->paginate($perPage, ['*'], 'notificationsPage');
    }

    public function latest(User $user): ?FlowNotification
    {
        return $this->visibleQuery($user)->latest('created_at')->latest('id')->first();
    }

    public function unreadCount(User $user): int
    {
        return $this->visibleQuery($user)->whereNull('read_at')->count();
    }

    public function markAllRead(User $user): void
    {
        $this->visibleQuery($user)->whereNull('read_at')->update(['read_at' => now()]);
        app(DashboardService::class)->forgetMentions($user);
        app(ShellDataService::class)->forget($user->id);
    }

    public function notifyUser(
        User $recipient,
        string $title,
        string $message,
        string $type = 'info',
        ?FlowJob $job = null,
        ?Task $task = null,
        ?User $actor = null,
    ): ?FlowNotification {
        if (!$recipient->is_active) return null;

        $access = app(AccessControlService::class);
        if ($task) {
            $visible = $access->applyTaskScope(Task::query()->whereKey($task->id), $recipient)->exists();
            if (!$visible) return null;
            $job ??= $task->job()->first();
        } elseif ($job) {
            $visible = $access->applyJobScope(FlowJob::query()->whereKey($job->id), $recipient)->exists();
            if (!$visible) return null;
        }

        $notification = FlowNotification::create([
            'user_id' => $recipient->id,
            'flow_job_id' => $job?->id,
            'flow_task_id' => $task?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);

        $this->forgetRecipientCaches($recipient);
        $this->deliverRealtime($recipient, $notification, $job, $task);

        return $notification;
    }

    public function notifyJobParticipants(
        FlowJob $job,
        string $title,
        string $message,
        string $type = 'info',
        ?User $actor = null,
        array $extraUserIds = [],
        array $excludeUserIds = [],
    ): void {
        $excluded = array_map('intval', $excludeUserIds);
        $assignedUserId = (int) (($job->owner_id ?? null) ?: ($job->coordinator_id ?? 0));
        $ids = collect($assignedUserId > 0 ? [$assignedUserId] : [])
            ->merge($extraUserIds)
            ->merge($this->administratorIds())
            ->filter()
            ->reject(fn ($id) => in_array((int) $id, $excluded, true))
            ->unique()->values()->all();

        $this->fanOutAfterCommit($ids, $title, $message, $type, $job->id, null, $actor?->id);
    }

    public function notifyTaskParticipants(
        Task $task,
        string $title,
        string $message,
        string $type = 'info',
        ?User $actor = null,
        array $extraUserIds = [],
        array $excludeUserIds = [],
    ): void {
        $excluded = array_map('intval', $excludeUserIds);
        $task->loadMissing('job.members');
        $job = $task->job;
        $ids = collect([$task->assignee_id, $job?->owner_id, $job?->coordinator_id])
            ->merge($job?->members?->pluck('user_id') ?? collect())
            ->merge($extraUserIds)
            ->merge($actor ? [$actor->id] : [])
            ->filter()
            ->reject(fn ($id) => in_array((int) $id, $excluded, true))
            ->unique()->values();

        if ($type === 'risk') $ids = $ids->merge($this->administratorIds())->unique()->values();

        $this->fanOutAfterCommit($ids->all(), $title, $message, $type, $job?->id, $task->id, $actor?->id);
    }

    public function backfillAdministratorMentions(User $administrator): void
    {
        if (! $administrator->is_active || ! app(AccessControlService::class)->isAdministrator($administrator)) {
            return;
        }

        $seen = [];
        $now = now();

        FlowNotification::query()
            ->where('type', 'mention')
            ->where(function (Builder $query): void {
                $query->whereNotNull('flow_job_id')
                    ->orWhereNotNull('flow_task_id')
                    ->orWhereNotNull('inquiry_id')
                    ->orWhereNotNull('inquiry_task_id');
            })
            ->chunkById(500, function (Collection $notifications) use ($administrator, &$seen, $now): void {
                foreach ($notifications as $source) {
                    $signature = hash('sha256', implode('|', [
                        (string) ($source->flow_job_id ?? ''),
                        (string) ($source->flow_task_id ?? ''),
                        (string) ($source->inquiry_id ?? ''),
                        (string) ($source->inquiry_task_id ?? ''),
                        (string) $source->title,
                        (string) $source->message,
                        (string) $source->created_at,
                    ]));

                    if (isset($seen[$signature])) continue;
                    $seen[$signature] = true;

                    $alreadyHasEvent = FlowNotification::query()
                        ->where('user_id', $administrator->id)
                        ->whereIn('type', ['mention', 'mention_admin'])
                        ->where('flow_job_id', $source->flow_job_id)
                        ->where('flow_task_id', $source->flow_task_id)
                        ->where('inquiry_id', $source->inquiry_id)
                        ->where('inquiry_task_id', $source->inquiry_task_id)
                        ->where('message', $source->message)
                        ->where('created_at', $source->created_at)
                        ->exists();

                    if ($alreadyHasEvent) continue;

                    FlowNotification::query()->create([
                        'user_id' => $administrator->id,
                        'flow_job_id' => $source->flow_job_id,
                        'flow_task_id' => $source->flow_task_id,
                        'inquiry_id' => $source->inquiry_id,
                        'inquiry_task_id' => $source->inquiry_task_id,
                        'type' => 'mention_admin',
                        'title' => $this->administratorMentionTitle((string) $source->title),
                        'message' => $source->message,
                        'read_at' => $now,
                        'created_at' => $source->created_at,
                        'updated_at' => $now,
                    ]);
                }
            });

        $this->forgetRecipientCaches($administrator);
    }

    public function notifyMentionedUsers(
        array $recipientIds,
        string $title,
        string $message,
        ?FlowJob $job = null,
        ?Task $task = null,
        ?User $actor = null,
    ): void {
        $directIds = collect($recipientIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($directIds->isEmpty()) return;

        // Every tagged comment is also copied to Admin/Super Admin so their
        // dashboard is a workspace-wide mention feed. Directly tagged admins
        // receive only the normal copy, never a duplicate administrator copy.
        $ids = $directIds->merge($this->administratorIds())->unique()->values();
        $directLookup = array_fill_keys($directIds->all(), true);
        $jobId = $job?->id;
        $taskId = $task?->id;
        $actorId = $actor?->id;

        $this->runAfterCommit(function () use ($ids, $directLookup, $title, $message, $jobId, $taskId, $actorId): void {
            $job = $jobId ? FlowJob::withTrashed()->find($jobId) : null;
            $task = $taskId ? Task::withTrashed()->find($taskId) : null;
            $actor = $actorId ? User::find($actorId) : null;

            User::query()
                ->whereIn('id', $ids->all())
                ->where('is_active', true)
                ->get()
                ->each(function (User $recipient) use ($directLookup, $title, $message, $job, $task, $actor): void {
                    $direct = isset($directLookup[(int) $recipient->id]);
                    $this->createMentionNotification(
                        $recipient,
                        $direct ? $title : $this->administratorMentionTitle($title),
                        $message,
                        $job,
                        $task,
                        $actor,
                        $direct ? 'mention' : 'mention_admin',
                    );
                });
        });
    }

    public function notifyInquiryMentionedUsers(
        array $recipientIds,
        string $title,
        string $message,
        Inquiry $inquiry,
        ?InquiryTask $inquiryTask = null,
        ?User $actor = null,
    ): void {
        $directIds = collect($recipientIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($directIds->isEmpty()) return;

        $ids = $directIds->merge($this->administratorIds())->unique()->values();
        $directLookup = array_fill_keys($directIds->all(), true);
        $inquiryId = (int) $inquiry->id;
        $inquiryTaskId = $inquiryTask?->id ? (int) $inquiryTask->id : null;

        $this->runAfterCommit(function () use ($ids, $directLookup, $title, $message, $inquiryId, $inquiryTaskId): void {
            $inquiry = Inquiry::withTrashed()->find($inquiryId);
            if (!$inquiry || $inquiry->trashed()) return;

            $inquiryTask = $inquiryTaskId ? InquiryTask::withTrashed()->find($inquiryTaskId) : null;
            if ($inquiryTask?->trashed()) $inquiryTask = null;

            User::query()
                ->whereIn('id', $ids->all())
                ->where('is_active', true)
                ->get()
                ->each(function (User $recipient) use ($directLookup, $title, $message, $inquiry, $inquiryTask): void {
                    $visible = app(InquiryService::class)
                        ->visibleQuery($recipient)
                        ->whereKey($inquiry->id)
                        ->exists();
                    if (!$visible) return;

                    $direct = isset($directLookup[(int) $recipient->id]);
                    $notification = FlowNotification::create([
                        'user_id' => $recipient->id,
                        'flow_job_id' => null,
                        'flow_task_id' => null,
                        'inquiry_id' => $inquiry->id,
                        'inquiry_task_id' => $inquiryTask?->id,
                        'type' => $direct ? 'mention' : 'mention_admin',
                        'title' => $direct ? $title : $this->administratorMentionTitle($title),
                        'message' => $message,
                    ]);

                    $this->forgetRecipientCaches($recipient);
                    $this->deliverRealtime($recipient, $notification, null, null, $inquiry, $inquiryTask);
                });
        });
    }

    public function notifyTaskAssigned(Task $task, ?User $actor = null): void
    {
        if (!$task->assignee_id) return;
        $task->loadMissing(['job','phase','assignee']);
        if (!$task->assignee) return;

        $this->fanOutAfterCommit(
            [$task->assignee->id],
            'Task assigned: '.$task->title,
            ($task->job?->displayOrderNumber() ? $task->job->displayOrderNumber().' · ' : '').($task->phase?->name ?: 'Task').' · '.($task->due_date?->format('M j, Y') ?: 'No due date'),
            'assignment',
            $task->job?->id,
            $task->id,
            $actor?->id,
        );
    }

    public function notifyJobAssigned(
        FlowJob $job,
        ?User $actor = null,
        array $extraUserIds = [],
        array $excludeUserIds = [],
    ): void {
        $this->notifyJobParticipants(
            $job,
            'Job assigned: '.$job->title,
            $job->displayOrderNumber().' · '.($job->client?->name ?: 'Client').' · '.($job->phase?->name ?: 'Workflow started'),
            'assignment',
            $actor,
            $extraUserIds,
            $excludeUserIds,
        );
    }

    public function urlFor(FlowNotification $notification): string
    {
        // Always open through the notification resolver. It verifies current
        // access, repairs stale job/task pairings, marks the notification read,
        // and deep-links comment notifications to the exact comment when possible.
        return route('notifications.open', ['notification' => $notification->id]);
    }

    private function createMentionNotification(
        User $recipient,
        string $title,
        string $message,
        ?FlowJob $job,
        ?Task $task,
        ?User $actor,
        string $type = 'mention',
    ): ?FlowNotification {
        if (!$recipient->is_active) return null;

        $access = app(AccessControlService::class);
        $notificationJob = $job && !$job->trashed() ? $job : null;
        $notificationTask = $task && !$task->trashed() ? $task : null;

        if ($notificationTask) {
            $canOpenTask = $access->applyTaskScope(Task::query()->whereKey($notificationTask->id), $recipient)->exists();
            if (!$canOpenTask) {
                $notificationJob = null;
                $notificationTask = null;
            } else {
                $notificationJob ??= $notificationTask->job()->first();
            }
        } elseif ($notificationJob) {
            $canOpenJob = $access->applyJobScope(FlowJob::query()->whereKey($notificationJob->id), $recipient)->exists();
            if (!$canOpenJob) $notificationJob = null;
        }

        $notification = FlowNotification::create([
            'user_id' => $recipient->id,
            'flow_job_id' => $notificationJob?->id,
            'flow_task_id' => $notificationTask?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);

        $this->forgetRecipientCaches($recipient);
        $this->deliverRealtime($recipient, $notification, $notificationJob, $notificationTask);

        return $notification;
    }

    private function fanOutAfterCommit(
        array $recipientIds,
        string $title,
        string $message,
        string $type,
        ?int $jobId,
        ?int $taskId,
        ?int $actorId,
    ): void {
        $ids = collect($recipientIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        if ($ids === []) return;

        $this->runAfterCommit(function () use ($ids, $title, $message, $type, $jobId, $taskId, $actorId): void {
            $job = $jobId ? FlowJob::withTrashed()->find($jobId) : null;
            $task = $taskId ? Task::withTrashed()->find($taskId) : null;
            $actor = $actorId ? User::find($actorId) : null;
            $visibleJob = $job && !$job->trashed() ? $job : null;
            $visibleTask = $task && !$task->trashed() ? $task : null;

            User::query()
                ->whereIn('id', $ids)
                ->where('is_active', true)
                ->get()
                ->each(fn (User $recipient) => $this->notifyUser($recipient, $title, $message, $type, $visibleJob, $visibleTask, $actor));
        });
    }

    private function deliverRealtime(
        User $recipient,
        FlowNotification $notification,
        ?FlowJob $job,
        ?Task $task,
        ?Inquiry $inquiry = null,
        ?InquiryTask $inquiryTask = null,
    ): void {
        $pusher = app(PusherChannelService::class);
        if (!$pusher->enabled()) return;

        $payload = [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => app(RichTextService::class)->plainText($notification->message),
            'job_id' => $job?->id,
            'job_number' => $job?->displayOrderNumber(),
            'task_id' => $task?->id,
            'task_number' => $task?->task_number,
            'inquiry_id' => $inquiry?->id,
            'inquiry_number' => $inquiry?->inquiry_number,
            'inquiry_task_id' => $inquiryTask?->id,
            'url' => $this->urlFor($notification),
            'created_at' => $notification->created_at?->toIso8601String(),
            'unread_count' => $this->unreadCount($recipient),
        ];

        // Never make a Livewire/browser request wait for an external Pusher
        // HTTP call. Realtime delivery belongs on the queue so a slow or
        // unreachable Pusher endpoint cannot turn a successful database
        // action into a 30-second timeout.
        try {
            DeliverRealtimeNotification::dispatch(
                $recipient->id,
                'flowtrack.notification',
                $payload,
            )
                ->onConnection((string) config('services.pusher.queue_connection', 'database'))
                ->afterCommit();
        } catch (\Throwable $exception) {
            // The database notification is already saved. Realtime is an
            // enhancement only, so queue problems must never fail the user's
            // original action.
            Log::warning('Realtime notification could not be queued.', [
                'notification_id' => $notification->id,
                'user_id' => $recipient->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function forgetRecipientCaches(User $recipient): void
    {
        app(DashboardService::class)->forget($recipient->id);
        app(ReportService::class)->forget($recipient->id);
        app(ShellDataService::class)->forget($recipient->id);
    }

    private function runAfterCommit(callable $callback): void
    {
        // Notifications are a side effect, never part of the user's core save.
        // A notification/database fan-out problem after a successful commit must
        // not turn an inline edit into a false failure and make the UI roll back.
        $safeCallback = static function () use ($callback): void {
            try {
                $callback();
            } catch (\Throwable $exception) {
                Log::warning('Post-commit notification work failed.', [
                    'error' => $exception->getMessage(),
                    'exception' => $exception::class,
                ]);
            }
        };

        if (DB::transactionLevel() > 0) {
            DB::afterCommit($safeCallback);
            return;
        }

        $safeCallback();
    }

    private function administratorMentionTitle(string $title): string
    {
        $converted = str_replace(' mentioned you in ', ' mentioned a user in ', $title);
        return $converted !== $title ? $converted : 'Tagged comment: '.$title;
    }

    private function administratorIds(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('is_super_admin', true)
                    ->orWhereHas('role', fn ($r) => $r->whereIn('slug', ['super-admin','admin','administrator']));
            })
            ->pluck('id');
    }
}
