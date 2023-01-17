<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class GroupSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_leader_can_access_settings_pages()
    {
        $leader = $this->user();
        $member = $this->user();

        $group = Group::factory()->create(['user_id' => $leader->id]);
        $group->members()->attach(2);

        $this->actingAs($member);

        $this->get("/groups/$group->slug/settings/info")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/delete")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/remove-members")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/manage-roles")
            ->assertForbidden();
    }

    public function test_user_cannot_access_others_group_settings_pages()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $user2 = $this->user();
        $group2 = Group::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user2);

        $this->get("/groups/$group->slug/settings/info")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/delete")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/remove-members")
            ->assertForbidden();

        $this->get("/groups/$group->slug/settings/manage-roles")
            ->assertForbidden();
    }

    public function test_return_edit_group_info_component()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->get("/groups/$group->slug/settings/info")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/EditGroupInfo')
                    ->has('group');
            });
    }

    public function test_return_delete_group_component()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->get("/groups/$group->slug/settings/delete")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/DeleteGroup')
                    ->has('group');
            });
    }

    public function test_return_remove_members_component()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->get("/groups/$group->slug/settings/remove-members")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/RemoveMembers')
                    ->has('group')
                    ->has('members');
            });
    }

    public function test_return_manage_roles_component()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->get("/groups/$group->slug/settings/manage-roles")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/ManageRoles')
                    ->has('group')
                    ->has('members');
            });
    }

    public function test_return_group_received_requests()
    {
        $user = $this->user();
        $user2 = $this->user();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user2->id]);

        $this->post("/groups/$group->id/join");

        $this->actingAs($user2);

        $response = $this->get("/groups/$group->slug/settings/join-requests");

        $response
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/JoinRequests')
                    ->has('group')
                    ->has('joinRequests');
            });
    }

    public function test_return_invite_members_components()
    {
        $this->withoutExceptionHandling();

        $user = $this->user();
        $this->actingAs($user);

        $user2 = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user2->id
        ]);

        $response = $this->get("/groups/$group->slug/settings/invite-members");

        $response
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Settings/InviteMembers')
                    ->has('group')
                    ->has('joinRequestsCount')
                    ->has('invitations');
            });
    }
}
