<?php

namespace Tests\Feature;

use Tests\TestCase;

class ListFilterExperienceTest extends TestCase
{
    public function test_shared_large_filter_runtime_limits_remote_results_and_cancels_stale_requests(): void
    {
        $service = file_get_contents(app_path('Services/FilterOptionService.php'));
        $runtime = file_get_contents(public_path('js/flowtrack-list-filters.js'));

        $this->assertStringContainsString('min(20, $limit)', $service);
        $this->assertStringContainsString('strlen($search) >= 2', $service);
        $this->assertStringContainsString('new AbortController()', $runtime);
        $this->assertStringContainsString('this.controller?.abort()', $runtime);
        $this->assertStringContainsString("x-on:input.debounce.300ms", file_get_contents(resource_path('views/components/ui/remote-filter.blade.php')));
    }

    public function test_dropdown_repositioning_measures_unconstrained_height_so_scroll_cannot_permanently_shrink_it(): void
    {
        $runtime = file_get_contents(public_path('js/flowtrack-list-filters.js'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('measureNaturalMenuHeight', $runtime);
        $this->assertStringContainsString("menu.style.setProperty('max-height', 'none', 'important')", $runtime);
        $this->assertStringContainsString('const measuredHeight = menu.scrollHeight;', $runtime);
        $this->assertStringContainsString('const naturalHeight = measureNaturalMenuHeight(menu, heightCap);', $runtime);
        $this->assertStringContainsString('/js/flowtrack-list-filters.js?v=20260810-client-selection-atomic-3', $layout);
    }

    public function test_remote_selector_never_pairs_a_new_value_with_the_previous_options_label(): void
    {
        $runtime = file_get_contents(public_path('js/flowtrack-list-filters.js'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('knownLabels: initialLabels', $runtime);
        $this->assertStringContainsString('const knownLabel = this.knownLabels.get(next);', $runtime);
        $this->assertStringContainsString('syncSelection(selection, params = {}, serverItems = [])', $runtime);
        $this->assertStringContainsString('if (this.pendingAt)', $runtime);
        $this->assertStringContainsString('if ((Date.now() - this.pendingAt) < 15000) return;', $runtime);
        $this->assertStringNotContainsString('currentLabel || suppliedLabel', $runtime);
        $this->assertStringContainsString('const resolved = item?.label || knownLabel || suppliedLabel || next;', $runtime);
        $this->assertStringContainsString('this.knownLabels.set(next, nextLabel);', $runtime);
        $this->assertStringContainsString('/js/flowtrack-list-filters.js?v=20260810-client-selection-atomic-3', $layout);
    }

    public function test_list_pages_share_the_same_filter_components(): void
    {
        $views = [
            resource_path('views/components/jobs/table.blade.php'),
            resource_path('views/livewire/clients/index.blade.php'),
            resource_path('views/livewire/documents/index.blade.php'),
        ];

        foreach ($views as $view) {
            $source = file_get_contents($view);
            $this->assertStringContainsString('ft-list-filter-shell', $source, $view);
            $this->assertStringContainsString('x-ui.list-search', $source, $view);
            $this->assertStringContainsString('ft-list-filter-chip', $source, $view);
            $this->assertStringContainsString('Updating…', $source, $view);
        }

        $allTasks = file_get_contents(resource_path('views/livewire/board/index.blade.php'));
        $this->assertStringNotContainsString('ft-list-filter-shell', $allTasks);
        $this->assertStringContainsString('wire:model.live.debounce.400ms="search"', $allTasks);
    }

    public function test_heavy_filter_lookups_are_not_loaded_as_full_lists_on_board_my_work_or_documents(): void
    {
        $board = file_get_contents(app_path('Livewire/Board/Index.php'));
        $myWork = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $documents = file_get_contents(app_path('Livewire/Documents/Index.php'));

        $this->assertStringNotContainsString('$service->lookups($user', $board);
        $this->assertStringNotContainsString("->limit(250)\n            ->get(['id', 'job_number', 'title', 'client_id'])", $myWork);
        $this->assertStringContainsString('$this->showUpload', $documents);
        $this->assertStringContainsString("->options($user, 'jobs', 'documents'", $documents);
    }

    public function test_task_assignee_picker_uses_compact_initial_results_and_department_metadata(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/FilterOptionController.php'));
        $service = file_get_contents(app_path('Services/FilterOptionService.php'));

        $this->assertStringContainsString("$type === 'users' && $context === 'task-assignee'", $controller);
        $this->assertStringContainsString('? 5', $controller);
        $this->assertStringContainsString("with('department:id,name')", $service);
        $this->assertStringContainsString("'meta' => (string) ($row->department?->name ?: '')", $service);
    }

    public function test_create_job_product_filter_supports_legacy_products_missing_parent_links(): void
    {
        $service = file_get_contents(app_path('Services/FilterOptionService.php'));
        $master = file_get_contents(app_path('Services/MasterDataService.php'));

        $this->assertStringContainsString("whereNull('parent_id')", $service);
        $this->assertStringContainsString("where('description', $category)", $service);
        $this->assertStringContainsString("where('description', 'like', $category.' ·%')", $service);
        $this->assertStringContainsString('Older demo/legacy Product rows did not always have parent_id set.', $master);
    }

    public function test_my_work_uses_the_grouped_personal_work_prototype_instead_of_the_shared_filter_grid(): void
    {
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));

        $this->assertStringContainsString('ft-mywork-v2-metrics', $view);
        $this->assertStringContainsString('wire:model.live.debounce.650ms="search"', $view);
        $this->assertStringContainsString('Type 3 characters to search broadly.', $view);
        $this->assertStringContainsString('Results update after 650 ms', $view);
        $this->assertStringContainsString('Mentions (', $view);
        $this->assertStringContainsString('use WithPagination;', $component);
        $this->assertStringNotContainsString('ft-list-filter-shell', $view);
    }
}
