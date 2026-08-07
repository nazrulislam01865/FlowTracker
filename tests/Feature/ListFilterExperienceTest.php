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

    public function test_list_pages_share_the_same_filter_components(): void
    {
        $views = [
            resource_path('views/components/jobs/table.blade.php'),
            resource_path('views/livewire/my-work/index.blade.php'),
            resource_path('views/livewire/clients/index.blade.php'),
            resource_path('views/livewire/board/index.blade.php'),
            resource_path('views/livewire/documents/index.blade.php'),
        ];

        foreach ($views as $view) {
            $source = file_get_contents($view);
            $this->assertStringContainsString('ft-list-filter-shell', $source, $view);
            $this->assertStringContainsString('x-ui.list-search', $source, $view);
            $this->assertStringContainsString('ft-list-filter-chip', $source, $view);
            $this->assertStringContainsString('Updating…', $source, $view);
        }
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

    public function test_create_job_product_filter_supports_legacy_products_missing_parent_links(): void
    {
        $service = file_get_contents(app_path('Services/FilterOptionService.php'));
        $master = file_get_contents(app_path('Services/MasterDataService.php'));

        $this->assertStringContainsString("whereNull('parent_id')", $service);
        $this->assertStringContainsString("where('description', $category)", $service);
        $this->assertStringContainsString("where('description', 'like', $category.' ·%')", $service);
        $this->assertStringContainsString('Older demo/legacy Product rows did not always have parent_id set.', $master);
    }
}
