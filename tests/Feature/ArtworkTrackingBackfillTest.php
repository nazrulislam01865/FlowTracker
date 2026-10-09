<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use App\Models\FlowJob;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowPhase;
use App\Services\ArtworkTrackingBackfillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ArtworkTrackingBackfillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create truly historical records without the current token hook.
        config()->set('flowtrack.order_tracking_enabled', false);
        Storage::fake((string) config('flowtrack.document_disk', 'flowtrack_private'));
    }

    public function test_dry_run_reports_eligible_old_order_without_creating_anything(): void
    {
        $job = $this->historicalOrder(confirmed: true);

        $status = Artisan::call('flowtrack:backfill-artwork-tracking', [
            '--order' => $job->id,
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $status);
        $this->assertNull($job->fresh()->tracking_token);
        $this->assertSame(0, Document::where('category', Document::CATEGORY_ARTWORK_TRACKING_PDF)->count());
        $this->assertStringContainsString('PDFs eligible: 1', Artisan::output());
    }

    public function test_backfill_generates_pdf_and_qr_for_old_confirmed_order_only_once(): void
    {
        $job = $this->historicalOrder(confirmed: true);
        $service = app(ArtworkTrackingBackfillService::class);

        $first = $service->process($job);
        $this->assertSame('generated', $first['qr']);
        $this->assertSame('generated', $first['pdf']);
        $token = $job->fresh()->tracking_token;
        $this->assertNotEmpty($token);

        $pdf = Document::where('flow_job_id', $job->id)
            ->where('category', Document::CATEGORY_ARTWORK_TRACKING_PDF)
            ->firstOrFail();
        $this->assertSame(1, (int) $pdf->version);
        $this->assertSame('application/pdf', $pdf->mime_type);
        $this->assertSame('DOC-TRACK-'.$job->id.'-V1', $pdf->document_number);
        $this->assertTrue(Storage::disk((string) config('flowtrack.document_disk', 'flowtrack_private'))->exists($pdf->path));
        $this->assertStringStartsWith('%PDF-', Storage::disk((string) config('flowtrack.document_disk', 'flowtrack_private'))->get($pdf->path));

        $second = $service->process($job->fresh());
        $this->assertSame('existing', $second['qr']);
        $this->assertSame('existing', $second['pdf']);
        $this->assertSame($token, $job->fresh()->tracking_token);
        $this->assertSame(1, Document::where('category', Document::CATEGORY_ARTWORK_TRACKING_PDF)->count());
    }

    public function test_unconfirmed_old_order_receives_qr_but_never_fake_confirmed_pdf(): void
    {
        $job = $this->historicalOrder(confirmed: false);
        $result = app(ArtworkTrackingBackfillService::class)->process($job);

        $this->assertSame('generated', $result['qr']);
        $this->assertSame('skipped', $result['pdf']);
        $this->assertNotEmpty($job->fresh()->tracking_token);
        $this->assertDatabaseMissing('documents', [
            'flow_job_id' => $job->id,
            'category' => Document::CATEGORY_ARTWORK_TRACKING_PDF,
        ]);
    }

    public function test_artwork_uploaded_after_the_last_review_cannot_be_backfilled_as_confirmed(): void
    {
        $job = $this->historicalOrder(confirmed: true);
        $review = Task::where('flow_job_id', $job->id)
            ->where('title', 'Internal Artwork Review')->firstOrFail();
        $review->update(['completed_at' => now()->subDay()]);

        $result = app(ArtworkTrackingBackfillService::class)->process($job);

        $this->assertSame('generated', $result['qr']);
        $this->assertSame('skipped', $result['pdf']);
        $this->assertStringContainsString('changed since', $result['reason']);
    }

    public function test_command_handles_unconfirmed_old_orders_without_failing_the_batch(): void
    {
        $job = $this->historicalOrder(confirmed: false);

        $this->assertSame(0, Artisan::call('flowtrack:backfill-artwork-tracking', ['--order' => $job->id]));
        $this->assertNotEmpty($job->fresh()->tracking_token);
        $this->assertStringContainsString('Skipped PDFs: 1', Artisan::output());
        $this->assertSame(0, Document::where('category', Document::CATEGORY_ARTWORK_TRACKING_PDF)->count());
    }

    public function test_single_order_generation_is_restricted_to_authorized_users(): void
    {
        config()->set('flowtrack.order_tracking_enabled', true);
        $job = $this->historicalOrder(confirmed: true);
        $admin = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('orders.tracking.generate', ['job' => $job->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'flow_job_id' => $job->id,
            'category' => Document::CATEGORY_ARTWORK_TRACKING_PDF,
        ]);
        $this->assertNotEmpty($job->fresh()->tracking_token);
    }

    private function historicalOrder(bool $confirmed): FlowJob
    {
        $client = Client::create([
            'name' => 'Legacy Artwork Client',
            'code' => 'LEGACY-'.uniqid(),
            'email' => 'legacy@example.com',
            'is_active' => true,
        ]);
        $workflow = Workflow::create([
            'name' => 'Historical Workflow',
            'slug' => 'historical-'.uniqid(),
            'is_active' => true,
        ]);
        $phase = WorkflowPhase::create([
            'workflow_id' => $workflow->id,
            'name' => 'Artwork',
            'short_name' => 'Artwork',
            'sequence' => 2,
            'is_active' => true,
        ]);
        $job = FlowJob::create([
            'workflow_id' => $workflow->id,
            'workflow_phase_id' => $phase->id,
            'client_id' => $client->id,
            'job_number' => 'FO-HIST-'.uniqid(),
            'title' => 'Historical branded item',
            'status' => 'active',
        ]);
        $upload = Task::create([
            'task_number' => 'TASK-UP-'.uniqid(),
            'flow_job_id' => $job->id,
            'workflow_phase_id' => $phase->id,
            'title' => 'Prepare & Upload Artwork',
            'status' => 'Completed',
            'completed_at' => now(),
        ]);
        Task::create([
            'task_number' => 'TASK-RV-'.uniqid(),
            'flow_job_id' => $job->id,
            'workflow_phase_id' => $phase->id,
            'title' => 'Internal Artwork Review',
            'status' => $confirmed ? 'Completed' : 'Ready',
            'completed_at' => $confirmed ? now()->addMinute() : null,
        ]);

        $path = 'flowtrack/documents/'.$job->id.'/artwork/test.png';
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+3LFEAAAAASUVORK5CYII=');
        Storage::disk((string) config('flowtrack.document_disk', 'flowtrack_private'))->put($path, $png);
        Document::create([
            'document_number' => 'DOC-LEGACY-'.uniqid(),
            'flow_job_id' => $job->id,
            'client_id' => $client->id,
            'task_id' => $upload->id,
            'category' => 'Artwork',
            'name' => 'test.png',
            'path' => $path,
            'mime_type' => 'image/png',
            'size' => strlen($png),
            'version' => 1,
        ]);

        return $job;
    }
}
