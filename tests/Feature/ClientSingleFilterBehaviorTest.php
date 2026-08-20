<?php

namespace Tests\Feature;

use Tests\TestCase;

class ClientSingleFilterBehaviorTest extends TestCase
{
    public function test_client_list_filters_and_summary_cards_are_mutually_exclusive(): void
    {
        $view = file_get_contents(resource_path('views/livewire/clients/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Clients/Index.php'));
        $css = $this->compatibilityCss('flowtrack-list-filters.css');

        $this->assertStringContainsString('class="ft-client-clear-filter"', $view);
        $this->assertStringContainsString('× Clear filter', $view);
        $this->assertStringContainsString('@disabled(! $clientAnyFilterActive)', $view);
        $this->assertStringContainsString("\$quick === 'all' && ! \$clientListFieldFilterActive", $view);

        $this->assertStringContainsString("\$this->activateSingleListFilter('search');", $component);
        $this->assertStringContainsString("\$this->activateSingleListFilter('manager');", $component);
        $this->assertStringContainsString("\$this->activateSingleListFilter('country');", $component);
        $this->assertStringContainsString("\$this->activateSingleListFilter('jobHealth');", $component);
        $this->assertStringContainsString("\$this->activateSingleListFilter('outstanding');", $component);
        $this->assertStringContainsString('private function activateSingleListFilter(string $activeFilter): void', $component);
        $this->assertStringContainsString('private function clearClientListFilterValues(?string $except = null): void', $component);
        $this->assertStringContainsString('$this->clearClientListFilterValues();', $component);
        $this->assertStringContainsString('.ft-client-clear-filter', $css);
    }
}
