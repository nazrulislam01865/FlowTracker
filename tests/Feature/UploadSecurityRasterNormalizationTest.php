<?php

namespace Tests\Feature;

use App\Services\UploadSecurityService;
use App\Support\StoredFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class UploadSecurityRasterNormalizationTest extends TestCase
{
    public function test_safe_raster_content_is_normalized_when_client_extension_is_wrong(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flowtrack-raster-');
        $this->assertNotFalse($path);

        try {
            // Enough of a JPEG signature for FlowTrack's security boundary. The
            // original client filename intentionally says PNG to reproduce the
            // production failure that prompted this regression test.
            file_put_contents($path, "\xFF\xD8\xFF\xE0".str_repeat("\x00", 64));

            $result = app(UploadSecurityService::class)->inspect(
                $path,
                'FO-333998.PNG',
                'image/png',
                1024,
            );

            $this->assertSame('clean', $result['status']);
            $this->assertSame('jpg', $result['extension']);
            $this->assertSame('image/jpeg', $result['mime']);
            $this->assertSame('image/jpeg', StoredFileResponse::mimeType('FO-333998.PNG', $result['mime']));
        } finally {
            @unlink($path);
        }
    }

    public function test_unknown_content_is_still_rejected_when_named_as_png(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flowtrack-raster-');
        $this->assertNotFalse($path);

        try {
            file_put_contents($path, 'not-an-image-file');

            try {
                app(UploadSecurityService::class)->inspect(
                    $path,
                    'not-really.PNG',
                    'image/png',
                    1024,
                );
                $this->fail('Expected the mismatched non-image upload to be rejected.');
            } catch (HttpExceptionInterface $exception) {
                $this->assertSame(422, $exception->getStatusCode());
                $this->assertStringContainsString('do not match its PNG file type', $exception->getMessage());
            }
        } finally {
            @unlink($path);
        }
    }
}
