<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SelectGroupTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function test_user_can_select_a_group_among_those_he_belongs_to_to_chat_with()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);

        $this->get("groups/$group->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Show')
                    ->has('group')
                    ->where('isMember', true);
            });
    }

    public function test_user_can_view_a_group_to_which_he_doesnt_belong()
    {
        $user2 = User::factory()->create();
        $group2 = Group::factory()->create(['user_id' => $user2->id]);

        $this->get("groups/$group2->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Show')
                    ->has('group')
                    ->where('isMember', false);
            });
    }

    public function test_leader_can_see_the_number_of_requests_to_join_the_group()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);

        $this->get("groups/$group->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Show')
                    ->has('group')
                    ->where('isMember', true)
                    ->has('joinRequests', 0);
            });
    }

    public function test_when_user_selects_a_group_the_related_messages_are_returned()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);

        $user2 = User::factory()->create();
        $group2 = Group::factory()->create(['user_id' => $user2->id]);

        $this->assertFalse($group2->members->contains($this->user));

        Message::factory()->create([
            'group_id' => $group->id,
            'from' => 2,
            'receiver_id' => $group2->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);


        Message::factory()->create([
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $this->get("groups/$group->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Show')
                    ->has('group')
                    ->has('messages', 1)
                    ->has('messages.0', function (AssertableInertia $page) {
                        $page
                            ->where('from', 1)
                            ->etc();
                    });
            });
    }

    public function test_when_user_selects_a_group_the_related_members_are_returned()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);

        User::factory()->create();

        $group->members()->attach(2);

        $this->get("groups/$group->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->has('group.members', 2);
            });
    }
}
