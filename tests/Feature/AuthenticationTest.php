<?php
namespace Tests\Feature;
use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class AuthenticationTest extends TestCase { use RefreshDatabase; public function test_guest_is_redirected_to_login():void{$this->get('/dashboard')->assertRedirect('/login');} public function test_authenticated_user_can_open_dashboard():void{$user=User::factory()->create(['is_super_admin'=>true]);$this->actingAs($user)->get('/dashboard')->assertOk();} }
