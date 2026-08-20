<?php

namespace Tests\Feature;

use Tests\TestCase;

class InquirySingleFilterBehaviorTest extends TestCase
{
    public function test_inquiry_list_has_explicit_clear_filter_action_and_exclusive_filter_logic(): void
    {
        $view = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $component = file_get_contents(app_path('Livewire/Inquiries/Index.php'));
        $css = file_get_contents(public_path('css/flowtrack-inquiries.css'));

        $this->assertStringContainsString('wire:click="clearFilters"', $view);
        $this->assertStringContainsString('Clear filter', $view);
        $this->assertStringContainsString('@disabled(! $inquiryAnyFilterActive)', $view);
        $this->assertStringContainsString('public function clearFilters(): void', $component);
        $this->assertStringContainsString("\$this->clearListFiltersExcept('search');", $component);
        $this->assertStringContainsString("\$this->clearListFiltersExcept('status');", $component);
        $this->assertStringContainsString("\$this->clearListFiltersExcept('client');", $component);
        $this->assertStringContainsString("\$this->clearListFiltersExcept('hideCompleted');", $component);
        $this->assertStringContainsString("\$this->clearListFiltersExcept('quick');", $component);
        $this->assertStringContainsString('private function clearListFiltersExcept(string $except): void', $component);
        $this->assertStringContainsString('.ft-inquiry-clear-filter', $css);
    }
}
