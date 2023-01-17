<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\JoinRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestToJoinGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_send_request_to_join_a_group()
    {
        $this->post("/groups/1/join")
            ->assertRedirect('/login');
    }

    public function test_user_can_send_a_request_to_join_a_group()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create();

        $this->post("/groups/$group->id/join");

        $this->assertDatabaseHas('join_requests', [
            'from' => $user->id,
            'to' => $group->id,
            'status' => 'Pending',
        ]);
    }

    public function test_if_user_already_sent_a_request_to_join_a_group_he_can_cancel_it()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create();

        $this->post("/groups/$group->id/join");

        $this->assertDatabaseHas('join_requests', [
            'from' => $user->id,
            'to' => $group->id,
            'status' => 'Pending',
        ]);

        $this->post("/groups/$group->id/join");

        $this->assertDatabaseMissing('join_requests', [
            'from' => $user->id,
            'to' => $group->id,
            'status' => 'Pending',
        ]);
    }

    public function test_check_the_request_status()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create();

        $this->post("/groups/$group->id/join");

        $response = $this->get("/groups/$group->id/request-status");

        $this->assertEquals('Pending', $response->json()['status']);
    }

    public function test_leader_can_reject_a_join_group_request()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user2);

        $this->post("/groups/$group->id/join");

        $this->actingAs($user);

        $joinRequest = JoinRequest::first();

        $this->assertEquals('Pending', $joinRequest->status);
        $this->assertEquals($user2->id, $joinRequest->from);

        $this->post("/groups/$group->id/reject-request", [
            'from' => $joinRequest->from
        ]);

        $this->assertEquals('Rejected', $joinRequest->fresh()->status);
    }

    public function test_leader_can_accept_a_join_group_request()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user2);

        $this->post("/groups/$group->id/join");

        $this->actingAs($user);

        $joinRequest = JoinRequest::first();

        $this->assertEquals('Pending', $joinRequest->status);

        $this->post("/groups/$group->id/accept-request", [
            'from' => $joinRequest->from
        ]);

        $this->assertEquals('Accepted', $joinRequest->fresh()->status);
    }

    public function test_accepting_a_join_group_request_add_member_to_group()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user2);

        $this->post("/groups/$group->id/join");

        $this->actingAs($user);

        $joinRequest = JoinRequest::first();

        $this->assertEquals(1, $group->members->count());

        $this->post("/groups/$group->id/accept-request", [
            'from' => $joinRequest->from
        ]);

        $this->assertEquals(2, $group->fresh()->members->count());
    }

    public function test_accepting_a_join_group_request_create_activity()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user2);

        $this->post("/groups/$group->id/join");

        $this->actingAs($user);

        $joinRequest = JoinRequest::first();

        $this->assertEquals(1, $group->members->count());

        $this->post("/groups/$group->id/accept-request", [
            'from' => $joinRequest->from
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'member_added',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }
}
