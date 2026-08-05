<?php

namespace Tests\Feature;

use App\Livewire\Clients\Index;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientArchiveRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_archived_listed_and_restored_without_hard_delete(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $client = Client::create(['code' => 'ARCH-001', 'name' => 'Archive Test', 'is_active' => true]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('deleteClient', $client->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'is_active' => false]);
        $this->assertFalse(app(ClientService::class)->filteredQuery($user)->whereKey($client->id)->exists());
        $this->assertTrue(app(ClientService::class)->filteredQuery($user, ['archived' => true])->whereKey($client->id)->exists());

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('showArchivedClients')
            ->assertSee('Archive Test')
            ->call('restoreClient', $client->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'is_active' => true]);
    }

    public function test_active_and_archived_rows_do_not_depend_on_a_blade_local_initials_variable(): void
    {
        $view = file_get_contents(resource_path('views/livewire/clients/index.blade.php'));

        $this->assertStringNotContainsString('$rowInitials', $view);
        $this->assertSame(2, substr_count($view, 'BoardPresenter::initials($clientRow->name)'));
    }
}
