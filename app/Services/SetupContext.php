<?php

namespace App\Services;

use App\Models\Workspace;

class SetupContext
{
    private ?int $resolvedWorkspaceId = null;

    public function workspaceId(): int
    {
        if ($this->resolvedWorkspaceId !== null) return $this->resolvedWorkspaceId;

        $configured = (int) config('flowtrack.workspace_id', 1);
        $workspace = Workspace::query()->whereKey($configured)->first()
            ?? Workspace::query()->where('is_active', true)->orderBy('id')->first();

        if ($workspace) return $this->resolvedWorkspaceId = (int) $workspace->id;

        return $this->resolvedWorkspaceId = (int) Workspace::query()->create([
            'id' => $configured ?: 1,
            'name' => 'FlowTrack',
            'slug' => 'flowtrack',
            'timezone' => config('app.timezone', 'Asia/Dhaka'),
            'default_currency' => 'USD',
            'is_active' => true,
        ])->id;
    }
}
