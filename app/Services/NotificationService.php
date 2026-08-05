<?php

namespace App\Services;

use App\Jobs\DeliverRealtimeNotification;
use App\Jobs\FanOutFlowNotification;
use App\Models\FlowJob;
use App\Models\FlowNotification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    public function list(User $user)
    {
        return FlowNotification::with(['job','task'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function paginate(User $user, int $perPage = 30)
    {
        return FlowNotification::with(['job','task'])
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
        if (!$access->can($recipient, 'notifications', 'view')) return null;

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

        app(DashboardService::class)->forget($recipient->id);
        app(ReportService::class)->forget($recipient->id);
        app(ShellDataService::class)->forget($recipient->id);
        if (app(PusherChannelService::class)->enabled()) {
            DeliverRealtimeNotification::dispatch($recipient->id, 'flowtrack.notification', [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'job_id' => $job?->id,
                'job_number' => $job?->job_number,
                'task_id' => $task?->id,
                'task_number' => $task?->task_number,
                'url' => $this->urlFor($notification),
                'created_at' => $notification->created_at?->toIso8601String(),
                'unread_count' => $this->unreadCount($recipient),
            ])->afterCommit();
        }

        return $notification;
    }

    public function notifyJobParticipants(
        FlowJob $job,
        string $title,
        string $message,
        string $type = 'info',
        ?User $actor = null,
        array $extraUserIds = [],
    ): void {
        // Job-level notifications are deliberately restricted to the Job's
        // assigned user plus Admin/Super Admin. Explicit extra recipients are
        // included for assignment changes, while generic viewers do not receive
        // Job noise merely because they can view the record.
        $assignedUserId = (int) (($job->owner_id ?? null) ?: ($job->coordinator_id ?? 0));
        $ids = collect($assignedUserId > 0 ? [$assignedUserId] : [])
            ->merge($extraUserIds)
            ->merge($this->administratorIds())
            ->filter()->unique()->values();

        FanOutFlowNotification::dispatch(
            $ids->all(),
            $title,
            $message,
            $type,
            $job->id,
            null,
            $actor?->id,
        )->afterCommit();
    }

    public function notifyTaskParticipants(
        Task $task,
        string $title,
        string $message,
        string $type = 'info',
        ?User $actor = null,
        array $extraUserIds = [],
    ): void {
        $task->loadMissing('job.members');
        $job = $task->job;
        $ids = collect([$task->assignee_id, $job?->owner_id, $job?->coordinator_id])
            ->merge($job?->members?->pluck('user_id') ?? collect())
            ->merge($extraUserIds)
            ->merge($actor ? [$actor->id] : [])
            ->filter()->unique()->values();

        if ($type === 'risk') $ids = $ids->merge($this->administratorIds())->unique()->values();

        FanOutFlowNotification::dispatch(
            $ids->all(),
            $title,
            $message,
            $type,
            $job?->id,
            $task->id,
            $actor?->id,
        )->afterCommit();
    }

    public function notifyTaskAssigned(Task $task, ?User $actor = null): void
    {
        if (!$task->assignee_id) return;
        $task->loadMissing(['job','phase','assignee']);
        if (!$task->assignee) return;

        FanOutFlowNotification::dispatch(
            [$task->assignee->id],
            'Task assigned: '.$task->title,
            ($task->job?->job_number ? $task->job->job_number.' · ' : '').($task->phase?->name ?: 'Task').' · '.($task->due_date?->format('M j, Y') ?: 'No due date'),
            'assignment',
            $task->job?->id,
            $task->id,
            $actor?->id,
        )->afterCommit();
    }

    public function notifyJobAssigned(FlowJob $job, ?User $actor = null, array $extraUserIds = []): void
    {
        $this->notifyJobParticipants(
            $job,
            'Job assigned: '.$job->title,
            $job->job_number.' · '.($job->client?->name ?: 'Client').' · '.($job->phase?->name ?: 'Workflow started'),
            'assignment',
            $actor,
            $extraUserIds,
        );
    }

    public function urlFor(FlowNotification $notification): string
    {
        if ($notification->flow_task_id) {
            return route('jobs.index', array_filter(['open' => $notification->flow_job_id, 'task' => $notification->flow_task_id]));
        }
        if ($notification->flow_job_id) return route('jobs.index', ['open' => $notification->flow_job_id]);
        return route('notifications');
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
