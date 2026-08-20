<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderTaskLinkPersistenceTest extends TestCase
{
    public function test_order_task_link_save_persists_before_the_inline_form_is_closed(): void
    {
        $service = file_get_contents(app_path('Services/TaskService.php'));
        $livewire = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $taskDetail = file_get_contents(resource_path('views/components/jobs/task-detail.blade.php'));
        $overview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $jobService = file_get_contents(app_path('Services/JobService.php'));

        $addStart = strpos($service, 'public function addExternalLink');
        $removeStart = strpos($service, 'public function removeExternalLink');
        $this->assertNotFalse($addStart);
        $this->assertNotFalse($removeStart);
        $addMethod = substr($service, $addStart, $removeStart - $addStart);

        $this->assertStringContainsString('$task->links()->create([', $addMethod);
        $this->assertStringContainsString('return $link->refresh();', $addMethod);
        $this->assertStringNotContainsString('$this->refreshJobState($task, $actor);', $addMethod);

        $this->assertStringContainsString('$task->links()->whereKey($link->id)->exists()', $livewire);
        $this->assertStringContainsString("'links.creator:id,name'", $livewire);
        $this->assertStringContainsString('private function hydrateLoadedTaskLinks(FlowJob $job): void', $jobService);
        $this->assertStringContainsString('TaskLink::query()', $jobService);
        $this->assertStringNotContainsString("setRelation('visibleTaskLinks'", $jobService);
        $this->assertStringContainsString("$task->setRelation(", $jobService);
        $this->assertStringContainsString("'links'", $jobService);
        $this->assertStringContainsString('$task->setRelation(', $jobService);
        $this->assertStringContainsString('task-detail-link-{{ $taskLink->id }}', $taskDetail);
        $this->assertStringContainsString('External link ·', $taskDetail);
        $presenter = file_get_contents(app_path('Support/JobDetailPresenter.php'));
        $this->assertStringContainsString('public static function taskLinks(FlowJob $job, Task $task): Collection', $presenter);
        $this->assertStringNotContainsString("visibleTaskLinks", $presenter);
        $this->assertStringContainsString("relationLoaded('links')", $presenter);
        $this->assertStringContainsString('JobDetailPresenter::taskLinks($job, $task)', $overview);
        $this->assertStringContainsString('job-task-resources-{{ $task->id }}-{{ $taskDocuments->count() }}-{{ $taskLinks->count() }}', $overview);
    }
}
