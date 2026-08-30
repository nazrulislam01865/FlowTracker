<?php

namespace Tests\Feature;

use App\Services\SecureDocumentStorage;
use App\Support\StoredFileResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class Phase10DocumentSecurityTest extends TestCase
{
    public function test_secure_storage_quarantines_scans_and_promotes_to_private_disk(): void
    {
        Storage::fake('flowtrack_private');
        Storage::fake('flowtrack_quarantine');
        Storage::fake('public');
        config()->set('flowtrack.document_disk', 'flowtrack_private');
        config()->set('flowtrack.quarantine_disk', 'flowtrack_quarantine');
        config()->set('flowtrack.legacy_document_disks', ['public']);
        config()->set('flowtrack.upload_security.scanner', 'basic');

        $file = UploadedFile::fake()->createWithContent('spec.pdf', "%PDF-1.7\nFlowTrack secure upload\n");
        $stored = app(SecureDocumentStorage::class)->store($file, 'flowtrack/documents/1');

        Storage::disk('flowtrack_private')->assertExists($stored['path']);
        Storage::disk('public')->assertMissing($stored['path']);
        $this->assertSame([], Storage::disk('flowtrack_quarantine')->allFiles('pending'));
        $this->assertStringStartsWith('flowtrack/documents/1/', $stored['path']);
        $this->assertNotSame('spec.pdf', basename($stored['path']));
    }

    public function test_script_upload_is_rejected_before_promotion(): void
    {
        Storage::fake('flowtrack_private');
        Storage::fake('flowtrack_quarantine');
        config()->set('flowtrack.document_disk', 'flowtrack_private');
        config()->set('flowtrack.quarantine_disk', 'flowtrack_quarantine');

        $file = UploadedFile::fake()->createWithContent('payload.php', '<?php echo "bad";');

        try {
            app(SecureDocumentStorage::class)->store($file, 'flowtrack/documents/1');
            $this->fail('A script upload must not be promoted.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertSame([], Storage::disk('flowtrack_private')->allFiles());
            $this->assertNotSame([], Storage::disk('flowtrack_quarantine')->allFiles('pending'));
        }
    }

    public function test_eps_and_postscript_like_files_are_never_rendered_inline(): void
    {
        Storage::fake('flowtrack_private');
        config()->set('flowtrack.document_disk', 'flowtrack_private');
        config()->set('flowtrack.legacy_document_disks', []);
        Storage::disk('flowtrack_private')->put('flowtrack/documents/1/art.eps', "%!PS-Adobe-3.0 EPSF-3.0\n");

        $response = StoredFileResponse::inline('flowtrack/documents/1/art.eps', 'customer-artwork.eps', 'application/postscript');
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        $this->assertSame('application/octet-stream', $response->headers->get('Content-Type'));
    }
}
