<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocumentArchiveActionMenuRegressionTest extends TestCase
{
    public function test_document_action_popover_closes_before_livewire_actions_mutate_the_page(): void
    {
        $view = file_get_contents(resource_path('views/livewire/documents/index.blade.php'));

        $this->assertStringContainsString('x-on:click.capture="', $view);
        $this->assertStringContainsString("const item = \$event.target.closest('[role=menuitem]');", $view);
        $this->assertStringContainsString("if (item && \$el.matches(':popover-open'))", $view);
        $this->assertStringContainsString('\$el.hidePopover();', $view);
    }
    public function test_document_archive_columns_have_fixed_non_overlapping_layout(): void
    {
        $view = file_get_contents(resource_path('views/livewire/documents/index.blade.php'));
        $css = file_get_contents(public_path('css/flowtrack-documents-archive.css'));

        $this->assertStringContainsString('<colgroup>', $view);
        $this->assertStringContainsString('ft-da-col-task', $view);
        $this->assertStringContainsString('ft-da-col-client', $view);
        $this->assertStringContainsString('min-width:1320px', $css);
        $this->assertStringContainsString('.ft-da-task-link{display:block;width:100%}', $css);
        $this->assertStringContainsString('.ft-da-record-cell a{display:block;flex:1 1 auto}', $css);
    }

}
