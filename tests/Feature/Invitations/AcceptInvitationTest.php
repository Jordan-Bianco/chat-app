<?php

namespace Tests\Feature\Invitations;

use App\Models\Group;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_all_invitations()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->assertEquals(1, JoinRequest::where('to', $user->id)->count());

        $this->actingAs($user);

        $response = $this->get("/profile/invitations");

        $response
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Profile/Invitations')
                    ->has('user')
                    ->has('invitations');
            });
    }

    public function test_user_can_reject_an_invitation()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->actingAs($user);

        $this->post("/groups/$group->id/reject-invitation");

        $this->assertDatabaseHas('join_requests', [
            'from' => $group->id,
            'to' => $user->id,
            'status' => 'Rejected'
        ]);
    }

    public function test_user_can_accept_an_invitation()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->actingAs($user);

        $this->post("/groups/$group->id/accept-invitation");

        $this->assertDatabaseHas('join_requests', [
            'from' => $group->id,
            'to' => $user->id,
            'status' => 'Accepted'
        ]);
    }

    public function test_if_user_accepts_an_invitation_he_is_added_to_the_group()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->actingAs($user);

        $this->post("/groups/$group->id/accept-invitation");

        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'User'
        ]);
    }

    public function test_if_user_accepts_an_invitation_activity_is_generated()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->actingAs($user);

        $this->post("/groups/$group->id/accept-invitation");

        $this->assertDatabaseHas('activities', [
            'author_id' => null,
            'name' => 'member_added',
            'subject_id' => $group->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }
}
