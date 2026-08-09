<?php

namespace Tests\Feature;

use Tests\TestCase;

class RichTextImagePasteSupportTest extends TestCase
{
    public function test_operational_descriptions_and_comments_use_the_shared_rich_text_editor(): void
    {
        $jobCreate = file_get_contents(resource_path('views/components/jobs/create.blade.php'));
        $jobOverview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $taskDetail = file_get_contents(resource_path('views/components/jobs/task-detail.blade.php'));
        $jobActivity = file_get_contents(resource_path('views/components/jobs/detail-activity.blade.php'));
        $inquiries = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $inquiryActivity = file_get_contents(resource_path('views/livewire/inquiries/_activity.blade.php'));

        $this->assertStringContainsString('data-rich-text wire:model="description"', $jobCreate);
        $this->assertStringContainsString('data-rich-text autocomplete="off"', $jobOverview);
        $this->assertGreaterThanOrEqual(2, substr_count($taskDetail, 'data-rich-text'));
        $this->assertStringContainsString('data-rich-text data-rich-text-compact wire:model="jobComment"', $jobActivity);
        $this->assertGreaterThanOrEqual(3, substr_count($inquiries, 'data-rich-text'));
        $this->assertStringContainsString('data-rich-text data-rich-text-compact wire:model="inquiryComment"', $inquiryActivity);
    }

    public function test_rich_text_runtime_uploads_clipboard_images_instead_of_storing_base64_html(): void
    {
        $runtime = file_get_contents(resource_path('js/rich-text.js'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString("meta[name=\"flowtrack-rich-text-upload-url\"]", $runtime);
        $this->assertStringContainsString('event.clipboardData?.items', $runtime);
        $this->assertStringContainsString('const clipboardFiles = directFiles.length ? directFiles : itemFiles;', $runtime);
        $this->assertStringContainsString('const seenFiles = new Set();', $runtime);
        $this->assertStringNotContainsString('const files = [...directFiles, ...itemFiles]', $runtime);
        $this->assertStringContainsString("body.append('image', file", $runtime);
        $this->assertStringContainsString("Route::post('/rich-text-images'", $routes);
        $this->assertStringContainsString("Route::get('/rich-text-images/{filename}'", $routes);
    }

    public function test_rich_text_is_sanitized_and_mentions_still_work(): void
    {
        $richText = file_get_contents(app_path('Services/RichTextService.php'));
        $mentions = file_get_contents(app_path('Services/MentionService.php'));

        $this->assertStringContainsString("strip_tags(\$html, '<p><div><br><strong><b><em><i><u><ul><ol><li><img>')", $richText);
        $this->assertStringContainsString('rich-text-images/([A-Za-z0-9-]+', $richText);
        $this->assertStringContainsString("route('rich-text-images.show', ['filename' => \$imageMatch[1]], false)", $richText);
        $this->assertStringContainsString('RichTextService::class)->plainText', $mentions);
        $this->assertStringContainsString('RichTextService::class)->safeHtml', $mentions);
    }

    public function test_rich_mention_notifications_keep_the_original_comment_for_existing_deep_links(): void
    {
        $notifications = file_get_contents(app_path('Services/NotificationService.php'));
        $dashboard = file_get_contents(app_path('Services/DashboardService.php'));

        $this->assertGreaterThanOrEqual(2, substr_count($notifications, "'message' => \$message"));
        $this->assertStringContainsString('dashboardMentionQuery', $dashboard);
        $this->assertStringContainsString("flow_notifications.flow_task_id", $dashboard);
        $this->assertStringContainsString("flow_notifications.inquiry_id", $dashboard);
        $this->assertStringNotContainsString("whereColumn('flow_task_comments.body', 'flow_notifications.message')", $dashboard);
        $this->assertStringNotContainsString("whereColumn('activities.description', 'flow_notifications.message')", $dashboard);
    }
    public function test_inline_description_saves_wait_for_images_and_use_one_rich_text_source_of_truth(): void
    {
        $runtime = file_get_contents(resource_path('js/rich-text.js'));
        $inlineEditing = file_get_contents(public_path('js/flowtrack-inline-editing.js'));
        $jobOverview = file_get_contents(resource_path('views/components/jobs/detail-overview.blade.php'));
        $taskDetail = file_get_contents(resource_path('views/components/jobs/task-detail.blade.php'));
        $inquiries = file_get_contents(resource_path('views/livewire/inquiries/index.blade.php'));
        $jobsIndex = file_get_contents(app_path('Livewire/Jobs/Index.php'));
        $inquiriesIndex = file_get_contents(app_path('Livewire/Inquiries/Index.php'));

        $this->assertStringContainsString('source.__flowtrackRichTextValueAsync = async () =>', $runtime);
        $this->assertStringContainsString('await Promise.allSettled(Array.from(pendingUploads))', $runtime);
        $this->assertStringContainsString('source.__flowtrackRichTextSetValue = (value) =>', $runtime);
        $this->assertStringContainsString('async saveRichText(source, emptyDisplay, requestFactory)', $inlineEditing);
        $this->assertStringContainsString('saveRichText($refs.descriptionEditor', $jobOverview);
        $this->assertStringContainsString('saveRichText($refs.description', $taskDetail);
        $this->assertStringContainsString('saveRichText($refs.inquiryDescription', $inquiries);
        $this->assertStringContainsString('hasRichTextOverride', $inlineEditing);
        $this->assertStringContainsString('richTextOverrideHtml', $inlineEditing);
        $this->assertStringContainsString('displayHtml', $jobsIndex);
        $this->assertStringContainsString('displayHtml', $inquiriesIndex);
        $this->assertStringNotContainsString("saveRichText($refs.descriptionEditor, 'No order description recorded.', (clean) => $wire.updateJobTextField({{ $job->id }}, 'description', clean)).then", $jobOverview);
        $this->assertStringNotContainsString("saveRichText($refs.description, 'No description has been provided for this task.', (clean) => $wire.updateSelectedTaskField('description', clean)).then", $taskDetail);
        $this->assertStringNotContainsString("saveRichText($refs.inquiryDescription, 'No description has been provided for this Inquiry.', (clean) => $wire.updateInquiryField('requirement_notes', clean)).then", $inquiries);
        $this->assertStringNotContainsString('x-ref="descriptionEditor" x-model="draftValue"', $jobOverview);
        $this->assertStringNotContainsString('x-ref="description" x-model="draftValue"', $taskDetail);
        $this->assertStringNotContainsString('x-ref="inquiryDescription" x-model="draftValue"', $inquiries);
    }

    public function test_rich_text_image_sanitizer_supports_app_subdirectory_urls(): void
    {
        $richText = file_get_contents(app_path('Services/RichTextService.php'));

        $this->assertStringContainsString("(?:^|/)rich-text-images/", $richText);
        $this->assertStringContainsString("\$safeUrl = route('rich-text-images.show'", $richText);
        $this->assertStringNotContainsString("#^/rich-text-images/", $richText);
    }

    public function test_saved_rich_text_images_have_zoom_preview_and_download_support(): void
    {
        $runtime = file_get_contents(resource_path('js/rich-text.js'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/RichTextImageController.php'));
        $css = file_get_contents(resource_path('css/flowtrack.css'));

        $this->assertStringContainsString(".ft-rich-text-content img", $runtime);
        $this->assertStringContainsString('bootRichTextImageViewer', $runtime);
        $this->assertStringContainsString('data-rich-image-download', $runtime);
        $this->assertStringContainsString('data-rich-image-zoom-in', $runtime);
        $this->assertStringContainsString('data-rich-image-zoom-out', $runtime);
        $this->assertStringContainsString("Route::get('/rich-text-images/{filename}/download'", $routes);
        $this->assertStringContainsString('public function download(string $filename)', $controller);
        $this->assertStringContainsString("->download($path, $filename", $controller);
        $this->assertStringContainsString('.ft-rich-image-viewer', $css);
        $this->assertStringContainsString('cursor:zoom-in', $css);
    }

    public function test_rich_text_survives_livewire_navigation_and_viewer_body_replacement(): void
    {
        $runtime = file_get_contents(resource_path('js/rich-text.js'));
        $app = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("'morph.added'", $runtime);
        $this->assertStringContainsString("'morphed'", $runtime);
        $this->assertStringContainsString('state.observedBody === document.body', $runtime);
        $this->assertStringContainsString('state.imageViewerController.ensureOverlay()', $runtime);
        $this->assertStringContainsString("document.addEventListener('livewire:navigating'", $app);
        $this->assertStringContainsString('event.detail?.onSwap?.(() => {', $app);
        $this->assertStringContainsString('scheduleRichTextRefresh();', $app);
    }

}
