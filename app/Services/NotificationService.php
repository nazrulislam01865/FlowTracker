<?php

namespace App\Services;

use App\Jobs\DeliverRealtimeNotification;
use App\Models\FlowJob;
use App\Models\FlowNotification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function list(User $user)
    {
        return FlowNotification::with(['job','task','inquiry','inquiryTask'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function paginate(User $user, int $perPage = 30)
    {
        return FlowNotification::with(['job','task','inquiry','inquiryTask'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage, ['*'], 'notificationsPage');
    }

    public function unreadCount(User $user): int
    {
        return FlowNotification::where('user_id', $user->id)->whereNull('read_at')->count();
    }

    public function markAllRead(User $user): void
    {
        FlowNotification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
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

    public function notifyMentionedUsers(
        array $recipientIds,
        string $title,
        string $message,
        ?FlowJob $job = null,
        ?Task $task = null,
        ?User $actor = null,
    ): void {
        $ids = collect($recipientIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->reject(fn ($id) => $actor && $id === (int) $actor->id)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) return;

        $jobId = $job?->id;
        $taskId = $task?->id;
        $actorId = $actor?->id;

        $this->runAfterCommit(function () use ($ids, $title, $message, $jobId, $taskId, $actorId): void {
            $job = $jobId ? FlowJob::withTrashed()->find($jobId) : null;
            $task = $taskId ? Task::withTrashed()->find($taskId) : null;
            $actor = $actorId ? User::find($actorId) : null;

            User::query()
                ->whereIn('id', $ids)
                ->where('is_active', true)
                ->get()
                ->each(fn (User $recipient) => $this->createMentionNotification($recipient, $title, $message, $job, $task, $actor));
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
            'type' => 'mention',
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
    ): void {
        $pusher = app(PusherChannelService::class);
        if (!$pusher->enabled()) return;

        $payload = [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'job_id' => $job?->id,
            'job_number' => $job?->displayOrderNumber(),
            'task_id' => $task?->id,
            'task_number' => $task?->task_number,
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
