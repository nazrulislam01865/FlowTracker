<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProgressivePageRenderingTest extends TestCase
{
    public function test_board_defers_heavy_cards_but_keeps_each_mode_branch_specific(): void
    {
        $component = file_get_contents(app_path('Livewire/Board/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/board/index.blade.php'));

        $this->assertStringContainsString('public bool $cardsReady = false;', $component);
        $this->assertStringContainsString('function loadBoardCards()', $component);
        $this->assertStringContainsString('$this->cardsReady', $component);
        $this->assertStringContainsString('wire:init="loadBoardCards"', $view);
        $this->assertStringContainsString('@if(!$cardsReady)', $view);
        $this->assertStringContainsString('livewire.shared.board-cards-placeholder', $view);
    }

    public function test_my_work_defers_task_cards_until_after_the_controls_render(): void
    {
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString('public bool $tasksReady = false;', $component);
        $this->assertStringContainsString('function loadMyWorkTasks()', $component);
        $this->assertStringContainsString('wire:init="loadMyWorkTasks"', $view);
        $this->assertStringContainsString('@if($tasksReady)', $view);
        $this->assertStringContainsString('livewire.shared.board-cards-placeholder', $view);
    }

    public function test_create_job_uses_viewport_loaded_sections(): void
    {
        $component = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $view = file_get_contents(resource_path('views/components/jobs/create.blade.php'));
        $placeholder = file_get_contents(resource_path('views/components/jobs/create-section-placeholder.blade.php'));

        $this->assertStringContainsString('function loadCreateSection(string $section)', $component);
        $this->assertStringContainsString('createCatalogReady', $component);
        $this->assertStringContainsString('createAssignmentReady', $component);
        $this->assertStringContainsString('createWorkflowReady', $component);
        $this->assertStringContainsString('@if($catalogReady)', $view);
        $this->assertStringContainsString('@if($assignmentReady)', $view);
        $this->assertStringContainsString('@if($workflowReady)', $view);
        $this->assertStringContainsString('IntersectionObserver', $placeholder);
    }

    public function test_documents_defer_rows_and_do_not_eager_load_every_job_task(): void
    {
        $component = file_get_contents(app_path('Livewire/Documents/Index.php'));
        $service = file_get_contents(app_path('Services/DocumentService.php'));
        $view = file_get_contents(resource_path('views/livewire/documents/index.blade.php'));

        $this->assertStringContainsString('public bool $documentsReady = false;', $component);
        $this->assertStringContainsString('function loadDocuments()', $component);
        $this->assertStringContainsString('wire:init="loadDocuments"', $view);
        $this->assertStringContainsString('livewire.documents.rows-placeholder', $view);
        $this->assertStringContainsString("withCount(['tasks'", $service);
        $this->assertStringNotContainsString("'job.tasks'", $service);
        $this->assertStringContainsString('function metrics(User $user): array', $service);
    }

    public function test_reports_render_kpis_before_secondary_charts(): void
    {
        $component = file_get_contents(app_path('Livewire/Reports/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/reports/index.blade.php'));

        $this->assertStringContainsString('public bool $secondaryReady = false;', $component);
        $this->assertStringContainsString('function loadSecondaryReports()', $component);
        $this->assertStringContainsString('wire:init="loadSecondaryReports"', $view);
        $this->assertStringContainsString('@if($secondaryReady)', $view);
        $this->assertStringContainsString('ft-report-scroll-bars', $view);
    }
}
