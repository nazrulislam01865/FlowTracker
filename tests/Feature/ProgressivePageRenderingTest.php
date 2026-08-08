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

    public function test_my_work_server_renders_a_bounded_first_page_instead_of_waiting_for_wire_init(): void
    {
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));
        $service = file_get_contents(app_path('Services/MyWorkService.php'));

        $this->assertStringNotContainsString('wire:init="loadMyWorkTasks"', $view);
        $this->assertStringNotContainsString('public bool $tasksReady', $component);
        $this->assertStringContainsString('MyWorkService::JOBS_PER_PAGE', $component);
        $this->assertStringContainsString('Paginate Jobs, never individual tasks.', $service);
        $this->assertStringContainsString('->paginate(', $service);
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
}
