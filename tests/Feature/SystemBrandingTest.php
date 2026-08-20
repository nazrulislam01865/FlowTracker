<?php

namespace Tests\Feature;

use App\Livewire\Administration\Index;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SystemBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_logo_and_it_is_served_without_public_storage_symlink(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $logo = UploadedFile::fake()->createWithContent('brand.png', $this->tinyPng());

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('tab', 'branding')
            ->set('logoUpload', $logo)
            ->call('saveLogo')
            ->assertHasNoErrors();

        $path = Workspace::query()->findOrFail(1)->logo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $response = $this->get('/branding-assets/logo/'.basename($path))
            ->assertOk();

        $this->assertCacheControlDirectives($response, ['public', 'max-age=31536000', 'immutable']);
    }

    public function test_admin_can_upload_favicon_and_login_uses_it(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $favicon = UploadedFile::fake()->createWithContent('favicon.png', $this->tinyPng());

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('tab', 'branding')
            ->set('faviconUpload', $favicon)
            ->call('saveFavicon')
            ->assertHasNoErrors();

        auth()->logout();
        $path = Workspace::query()->findOrFail(1)->favicon_path;
        $this->get('/login')
            ->assertOk()
            ->assertSee('/branding-assets/favicon/'.basename($path), false);
    }

    public function test_non_admin_cannot_change_system_branding(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_super_admin' => false, 'is_active' => true]);
        $logo = UploadedFile::fake()->createWithContent('brand.png', $this->tinyPng());

        $this->actingAs($user);

        try {
            app(BrandingService::class)->saveLogo($logo, $user);
            $this->fail('Expected a 403 response exception.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertNull(Workspace::query()->findOrFail(1)->logo_path);
    }

    private function tinyPng(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2kwsAAAAASUVORK5CYII=');
    }
}
