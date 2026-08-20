<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_setup_route_is_temporarily_disabled(): void
    {
        $user = User::factory()->create(['is_super_admin' => false]);

        $this->actingAs($user)->get('/workflow-setup')->assertNotFound();
    }
}
