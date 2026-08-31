<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\Role;
use App\Models\RoleModuleAccess;
use App\Models\User;
use App\Services\InquiryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryParticipantVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_sees_only_created_or_task_assigned_inquiries_even_with_all_records_role_scope(): void
    {
        $role = $this->inquiryRole('all_records');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_super_admin' => false,
            'is_active' => true,
        ]);
        $other = User::factory()->create(['is_active' => true]);
        $client = Client::create([
            'name' => 'Inquiry Participant Client',
            'code' => 'IPC-'.uniqid(),
            'is_active' => true,
        ]);

        $created = $this->inquiry($client, $user, $other, 'CREATED');
        $assigned = $this->inquiry($client, $other, $other, 'ASSIGNED');
        InquiryTask::create([
            'inquiry_id' => $assigned->id,
            'assignee_id' => $user->id,
            'title' => 'Assigned to target user',
            'sequence' => 1,
            'status' => 'Ready',
        ]);

        // Ownership by itself must not make a regular user's Inquiry list wider.
        $ownerOnly = $this->inquiry($client, $other, $user, 'OWNER');
        $unrelated = $this->inquiry($client, $other, $other, 'UNRELATED');

        $visibleIds = app(InquiryService::class)
            ->listQuery($user, [])
            ->pluck('inquiries.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->assertEqualsCanonicalizing([$created->id, $assigned->id], $visibleIds);
        $this->assertNotContains($ownerOnly->id, $visibleIds);
        $this->assertNotContains($unrelated->id, $visibleIds);

        // Summary cards must use the same visibility boundary as the list.
        $this->assertSame(2, app(InquiryService::class)->metrics($user)['createdToday']);
    }

    public function test_admin_and_super_admin_keep_full_inquiry_visibility(): void
    {
        $client = Client::create([
            'name' => 'Inquiry Admin Client',
            'code' => 'IAC-'.uniqid(),
            'is_active' => true,
        ]);
        $creator = User::factory()->create(['is_active' => true]);
        $owner = User::factory()->create(['is_active' => true]);

        $first = $this->inquiry($client, $creator, $owner, 'ADMIN-A');
        $second = $this->inquiry($client, $creator, $owner, 'ADMIN-B');

        $adminRole = Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            [
                'workspace_id' => 1,
                'name' => 'Admin',
                'code' => 'ADMIN',
                'description' => 'Administrator',
                'default_scope' => 'all_records',
                'is_system' => true,
                'is_active' => true,
                'sensitive_fields' => [],
            ],
        );
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_super_admin' => false,
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        foreach ([$admin, $superAdmin] as $actor) {
            $visibleIds = app(InquiryService::class)
                ->listQuery($actor, [])
                ->whereIn('inquiries.id', [$first->id, $second->id])
                ->pluck('inquiries.id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $this->assertEqualsCanonicalizing([$first->id, $second->id], $visibleIds);
        }
    }

    private function inquiryRole(string $scope): Role
    {
        $role = Role::create([
            'workspace_id' => 1,
            'name' => 'Inquiry Participant '.uniqid(),
            'slug' => 'inquiry-participant-'.uniqid(),
            'code' => 'INQ_PART_'.strtoupper(substr(md5((string) microtime(true)), 0, 8)),
            'description' => 'Inquiry participant visibility test role',
            'default_scope' => $scope,
            'is_system' => false,
            'is_active' => true,
            'sensitive_fields' => [],
        ]);

        RoleModuleAccess::create([
            'role_id' => $role->id,
            'module_code' => 'inquiries',
            'record_scope' => $scope,
            'actions' => ['view'],
        ]);

        return $role;
    }

    private function inquiry(Client $client, User $creator, User $owner, string $suffix): Inquiry
    {
        return Inquiry::create([
            'workspace_id' => 1,
            'inquiry_number' => 'INQ-PARTICIPANT-'.$suffix.'-'.uniqid(),
            'client_id' => $client->id,
            'owner_id' => $owner->id,
            'created_by' => $creator->id,
            'received_date' => today(),
            'subject' => 'Participant visibility '.$suffix,
            'status' => 'In Progress',
        ]);
    }
}
