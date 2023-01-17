<?php

namespace Tests\Feature\Invitations;

use App\Events\SearchUser;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InviteMemberToGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_can_search_user_by_name()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        User::factory()->create(['name' => 'john']);
        User::factory()->create(['name' => 'jane']);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        Event::fake();

        $this->get("/groups/$group->id/users?search=jane");

        Event::assertDispatched(function (SearchUser $event) {
            return $event->users->count() === 1;
        });
    }

    public function test_search_return_users_that_do_not_belongs_to_the_group()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        User::factory()->create(['name' => 'john doe']);
        User::factory()->create(['name' => 'jane doe']);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $group->members()->attach(2);

        Event::fake();

        $this->get("/groups/$group->id/users?search=doe");

        Event::assertDispatched(function (SearchUser $event) {
            return $event->users->count() === 1;
        });
    }

    public function test_search_return_users_that_are_not_already_invited()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $john = User::factory()->create(['name' => 'john doe']);
        $jane = User::factory()->create(['name' => 'jane doe']);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $john->id
        ]);

        Event::fake();

        $this->get("/groups/$group->id/users?search=doe");

        Event::assertDispatched(function (SearchUser $event) {
            return $event->users->count() === 1;
        });
    }

    public function test_leader_can_send_invitation_to_user()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ])
            ->assertSessionHas('message', 'Invitation sent!');

        $this->assertDatabaseHas('join_requests', [
            'from' => $group->id,
            'to' => $user->id,
            'status' => 'Pending'
        ]);
    }

    public function test_leader_can_cancel_an_invitation_sent_to_a_user()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->assertDatabaseHas('join_requests', [
            'from' => $group->id,
            'to' => $user->id,
            'status' => 'Pending'
        ]);

        $this->post("/groups/$group->id/invite-members", [
            'to' => $user->id
        ]);

        $this->assertDatabaseMissing('join_requests', [
            'from' => $group->id,
            'to' => $user->id,
            'status' => 'Pending'
        ]);
    }
}
