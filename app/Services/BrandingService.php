<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandingService
{
    public function current(): array
    {
        $configuredWorkspaceId = max(1, (int) config('flowtrack.workspace_id', 1));

        return Cache::remember($this->cacheKey(), now()->addHour(), function () use ($configuredWorkspaceId) {
            $workspace = Workspace::query()
                ->select(['id', 'name', 'logo_path', 'favicon_path'])
                ->whereKey($configuredWorkspaceId)
                ->first()
                ?? Workspace::query()
                    ->select(['id', 'name', 'logo_path', 'favicon_path'])
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->first();

            return [
                'workspace_id' => (int) ($workspace?->id ?: $configuredWorkspaceId),
                'name' => $workspace?->name ?: 'FlowTrack',
                'logo_path' => $workspace?->logo_path,
                'favicon_path' => $workspace?->favicon_path,
                'logo_url' => $this->assetUrl('logo', $workspace?->logo_path),
                'favicon_url' => $this->assetUrl('favicon', $workspace?->favicon_path),
            ];
        });
    }

    public function saveLogo(UploadedFile $file, ?User $actor = null): Workspace
    {
        $this->assertCanManage($actor ?: auth()->user());

        return $this->storeAsset('logo', 'logo_path', $file);
    }

    public function saveFavicon(UploadedFile $file, ?User $actor = null): Workspace
    {
        $this->assertCanManage($actor ?: auth()->user());

        return $this->storeAsset('favicon', 'favicon_path', $file);
    }

    public function removeLogo(?User $actor = null): Workspace
    {
        $this->assertCanManage($actor ?: auth()->user());

        return $this->removeAsset('logo_path');
    }

    public function removeFavicon(?User $actor = null): Workspace
    {
        $this->assertCanManage($actor ?: auth()->user());

        return $this->removeAsset('favicon_path');
    }

    public function assetUrl(string $type, ?string $path): ?string
    {
        if (!$path) return null;

        return '/branding-assets/'.$type.'/'.rawurlencode(basename($path));
    }

    private function storeAsset(string $type, string $column, UploadedFile $file): Workspace
    {
        $workspace = $this->workspace();
        $oldPath = $workspace->{$column};
        $clientExtension = strtolower((string) $file->getClientOriginalExtension());
        $allowedExtensions = $type === 'favicon'
            ? ['ico', 'png', 'jpg', 'jpeg', 'webp']
            : ['jpg', 'jpeg', 'png', 'webp'];
        $extension = in_array($clientExtension, $allowedExtensions, true)
            ? $clientExtension
            : strtolower($file->extension() ?: 'png');
        $filename = Str::uuid()->toString().'.'.$extension;
        $directory = 'branding/'.$workspace->id.'/'.$type;
        $path = $file->storeAs($directory, $filename, 'public');

        try {
            $workspace->update([$column => $path]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->forget();

        return $workspace->refresh();
    }

    private function removeAsset(string $column): Workspace
    {
        $workspace = $this->workspace();
        $oldPath = $workspace->{$column};

        $workspace->update([$column => null]);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->forget();

        return $workspace->refresh();
    }

    private function workspace(): Workspace
    {
        $configuredWorkspaceId = max(1, (int) config('flowtrack.workspace_id', 1));

        return Workspace::query()->whereKey($configuredWorkspaceId)->first()
            ?? Workspace::query()->where('is_active', true)->orderBy('id')->firstOrFail();
    }

    private function assertCanManage(?User $user): void
    {
        abort_unless(
            $user?->is_active && app(AccessControlService::class)->isAdministrator($user),
            403
        );
    }

    private function forget(): void
    {
        Cache::forget($this->cacheKey());
    }

    private function cacheKey(): string
    {
        return 'flowtrack:branding:workspace:'.max(1, (int) config('flowtrack.workspace_id', 1));
    }
}
