<?php

namespace Tests\Feature;

use App\Livewire\Clients\Index;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientPreviewModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_list_is_full_width_and_does_not_preselect_a_client(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $this->client();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSet('selectedClientId', null)
            ->assertSet('showClientPreview', false)
            ->assertSeeHtml('ft-clients-layout ft-clients-layout-full')
            ->assertDontSeeHtml('ft-client-preview-backdrop');
    }

    public function test_clicking_a_client_opens_and_closes_the_summary_modal(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $client = $this->client();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openClient', $client->id)
            ->assertSet('selectedClientId', $client->id)
            ->assertSet('showClientPreview', true)
            ->assertSee('ActiveWear Sports')
            ->assertSeeHtml('role="dialog"')
            ->assertSeeHtml('ft-client-preview-backdrop')
            ->call('closeClientPreview')
            ->assertSet('selectedClientId', null)
            ->assertSet('showClientPreview', false)
            ->assertDontSeeHtml('ft-client-preview-backdrop');
    }

    public function test_open_client_action_switches_from_preview_to_full_detail(): void
    {
        $user = User::factory()->create(['is_super_admin' => true, 'is_active' => true]);
        $client = $this->client();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openClient', $client->id)
            ->call('viewClient', $client->id)
            ->assertSet('showClientPreview', false)
            ->assertSet('showDetail', true)
            ->assertSee('Client Details');
    }

    private function client(): Client
    {
        return Client::create([
            'name' => 'ActiveWear Sports',
            'code' => 'CL-001',
            'country' => 'Australia',
            'contact_name' => 'Oliver Stone',
            'email' => 'oliver@activewear.au',
            'preferred_language' => 'English',
            'outstanding_balance' => 0,
            'is_active' => true,
        ]);
    }
}
