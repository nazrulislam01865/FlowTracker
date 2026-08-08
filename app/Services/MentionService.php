<?php

namespace App\Services;

use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MentionService
{
    private ?Collection $renderUsers = null;

    public function optionsForCreate(User $actor): Collection
    {
        return $this->activeUserOptions($actor);
    }

    public function optionsForJob(FlowJob $job, User $actor): Collection
    {
        return $this->activeUserOptions($actor);
    }

    public function optionsForTask(Task $task, User $actor): Collection
    {
        return $this->activeUserOptions($actor);
    }

    public function handle(User $user): string
    {
        return $this->baseHandle($user).'.'.$user->id;
    }

    public function userIdsFromText(?string $text): array
    {
        if (blank($text)) return [];

        $text = (string) $text;
        $ids = collect();

        // Canonical autocomplete token: @john.smith.42
        preg_match_all('/(?<![\pL\pN._-])@[\pL\pN][\pL\pN._-]*\.(\d+)\b/u', $text, $canonicalMatches);
        $ids = $ids->merge($canonicalMatches[1] ?? []);

        // Also accept an explicit portable token if content was pasted from another editor.
        preg_match_all('/@\[[^\]\r\n]+\]\((\d+)\)/u', $text, $portableMatches);
        $ids = $ids->merge($portableMatches[1] ?? []);

        // Accept a unique plain handle such as @john.smith. This keeps manual
        // typing useful while avoiding ambiguous notifications.
        preg_match_all('/(?<![\pL\pN._-])@([\pL\pN][\pL\pN._-]{0,80})/u', $text, $plainMatches);
        $plainTokens = collect($plainMatches[1] ?? [])
            ->map(fn ($token) => Str::lower(trim((string) $token, '._-')))
            ->filter()
            ->unique()
            ->values();

        $users = User::query()
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        if ($plainTokens->isNotEmpty()) {
            $aliases = [];

            foreach ($users as $user) {
                foreach ($this->aliasesFor($user) as $alias) {
                    $aliases[$alias] ??= [];
                    $aliases[$alias][] = (int) $user->id;
                }
            }

            foreach ($plainTokens as $token) {
                $matchingIds = array_values(array_unique($aliases[$token] ?? []));
                if (count($matchingIds) === 1) $ids->push($matchingIds[0]);
            }
        }

        $candidateIds = $ids
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($candidateIds->isEmpty()) return [];

        $activeIds = $users->pluck('id')->map(fn ($id) => (int) $id)->all();

        return $candidateIds
            ->filter(fn ($id) => in_array((int) $id, $activeIds, true))
            ->values()
            ->all();
    }

    public function render(?string $text): string
    {
        $text = (string) $text;
        $users = $this->renderUsers();
        $escaped = e($text);

        $rendered = preg_replace_callback(
            '/@\[([^\]\r\n]+)\]\((\d+)\)|(?<![\pL\pN._-])@([\pL\pN][\pL\pN._-]*\.(\d+))\b/u',
            function (array $match) use ($users): string {
                $id = (int) ($match[2] ?: ($match[4] ?? 0));
                $user = $users->get($id);
                if (!$user) return $match[0];

                return '<span class="ft-user-mention" title="'.e($user->name).'">@'.e($user->name).'</span>';
            },
            $escaped,
        );

        return nl2br($rendered ?? $escaped);
    }

    private function activeUserOptions(User $actor): Collection
    {
        return $this->formatUsers(
            User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
        );
    }

    private function formatUsers(Collection $users): Collection
    {
        return $users->map(fn (User $user) => [
            'id' => (int) $user->id,
            'name' => $user->name,
            'handle' => $this->handle($user),
        ])->values();
    }

    private function renderUsers(): Collection
    {
        return $this->renderUsers ??= User::query()
            ->where('is_active', true)
            ->get(['id', 'name', 'email'])
            ->keyBy('id');
    }

    private function baseHandle(User $user): string
    {
        $base = Str::of($user->name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->limit(45, '')
            ->toString();

        return $base !== '' ? $base : 'user';
    }

    private function aliasesFor(User $user): array
    {
        $base = Str::lower($this->baseHandle($user));
        $full = Str::lower($this->handle($user));
        $emailLocal = Str::lower(Str::before((string) $user->email, '@'));

        return collect([$base, $full, $emailLocal])
            ->map(fn ($alias) => trim((string) $alias, '._-'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
