<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderWorkflowPhaseCompletionColorTest extends TestCase
{
    public function test_order_workflow_bars_color_phases_only_after_all_phase_tasks_are_completed(): void
    {
        $presenter = file_get_contents(app_path('Support/JobDetailPresenter.php'));
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $workflow = file_get_contents(resource_path('views/components/jobs/detail-workflow.blade.php'));
        $css = file_get_contents(resource_path('css/flowtrack.css'));
        $runtimeColors = file_get_contents(public_path('css/flowtrack-master-colors.css'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('public static function isPhaseComplete', $presenter);
        $this->assertStringContainsString('$tasks->isNotEmpty() && self::completedCount($tasks) === $tasks->count()', $presenter);
        $this->assertStringContainsString('JobDetailPresenter::isPhaseComplete($job, $phase)', $overview);
        $this->assertStringContainsString("class=\"{{ \$phaseComplete ? 'done' : '' }}\"", $overview);
        $this->assertStringContainsString("{{ \$phaseComplete ? '✓' : \$phase->sequence }}", $overview);
        $this->assertStringContainsString('JobDetailPresenter::isPhaseComplete($job,$phase)', $workflow);
        $this->assertStringContainsString("ft-workflow-step {{ \$phaseComplete ? 'done' : '' }}", $workflow);
        $this->assertStringContainsString('.ft-overview-workflow-line span{width:24px;height:24px;position:relative;z-index:1;font-size:9px;background:#eef2f6;color:#64748b;border:1px solid #d7e0ea}', $css);
        $this->assertStringContainsString('.ft-overview-workflow-line .done span{background:var(--ft-master-color,#64748b)', $css);
        $this->assertStringContainsString('.ft-workflow-step span{width:28px;height:28px;margin:auto;border:1px solid #d7e0ea', $css);
        $this->assertStringContainsString('.ft-workflow-step.done span{background:var(--ft-master-color,#64748b)', $css);
        $this->assertStringContainsString('.ft-exact-job-detail .ft-workflow-mini-line button[style] span{', $runtimeColors);
        $this->assertStringContainsString('background:#eef2f6!important;', $runtimeColors);
        $this->assertStringContainsString('.ft-exact-job-detail .ft-workflow-mini-line button[style].done span{', $runtimeColors);
        $this->assertStringContainsString('.ft-exact-job-detail .ft-workflow-step[style].done span{', $runtimeColors);
        $this->assertStringNotContainsString('.ft-workflow-mini-line button[style].current span', $runtimeColors);
        $this->assertStringNotContainsString('.ft-workflow-step[style].done span,.ft-workflow-step[style].current span', $runtimeColors);
        $this->assertStringContainsString('flowtrack-master-colors.css?v=20260820-order-phase-completion-2', $layout);
    }
}
