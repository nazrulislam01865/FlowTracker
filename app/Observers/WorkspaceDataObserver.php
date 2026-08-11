<?php

namespace App\Observers;

use App\Services\WorkspaceRefreshService;
use Illuminate\Database\Eloquent\Model;

class WorkspaceDataObserver
{
    public function created(Model $model): void
    {
        $this->changed($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->changed($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->changed($model, 'deleted');
    }

    public function restored(Model $model): void
    {
        $this->changed($model, 'restored');
    }

    public function forceDeleted(Model $model): void
    {
        $this->changed($model, 'force-deleted');
    }

    private function changed(Model $model, string $action): void
    {
        app(WorkspaceRefreshService::class)->touch(class_basename($model).':'.$action);
    }
}
