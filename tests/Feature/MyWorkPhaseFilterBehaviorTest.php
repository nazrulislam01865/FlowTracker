<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyWorkPhaseFilterBehaviorTest extends TestCase
{
    public function test_my_work_phase_filter_is_order_workflow_based_and_deduplicated_by_name(): void
    {
        $service = file_get_contents(app_path('Services/MyWorkService.php'));

        $this->assertStringContainsString("->where('my_work_filter_workflows.applies_to', 'orders')", $service);
        $this->assertStringContainsString("->where('my_work_filter_workflows.is_active', true)", $service);
        $this->assertStringContainsString("->unique(fn (\$name) => mb_strtolower(\$name))", $service);
        $this->assertStringContainsString("LOWER(TRIM(workflow_phases.name)) = ?", $service);
        $this->assertStringContainsString('orderPhaseSourceIdsForName', $service);
        $this->assertStringContainsString("orWhereIn('workflow_phases.source_workflow_phase_id'", $service);
        $this->assertStringContainsString("'phase' => (string) (\$task->getAttribute('my_work_phase_short_name') ?: \$task->getAttribute('my_work_phase_name') ?: 'No phase')", $service);
    }

    public function test_metric_cards_and_toolbar_filters_are_mutually_exclusive(): void
    {
        $component = file_get_contents(app_path('Livewire/MyWork/Index.php'));
        $view = file_get_contents(resource_path('views/livewire/my-work/index.blade.php'));

        $this->assertStringContainsString('public bool $hideCompleted = false;', $component);
        $this->assertStringContainsString('public function setMetricFilter', $component);
        $this->assertStringContainsString("\$this->search = '';", $component);
        $this->assertStringContainsString("\$this->phaseFilter = '';", $component);
        $this->assertStringContainsString('clearMetricFilterForToolbar', $component);
        $this->assertStringContainsString('class="phase-toggle', $view);
        $this->assertStringContainsString('wire:click="setPhaseFilter(', $view);
        $this->assertStringNotContainsString('wire:model.live="phaseFilter"', $view);
        $this->assertStringContainsString('wire:click="clearFilters"', $view);
        $this->assertStringNotContainsString('Showing {{ $workGroups->count() }} of {{ $workPaginator->total() }} matching Orders', $view);
        $this->assertStringNotContainsString('Results update after 650 ms', $view);
    }
}
