<?php

namespace Tests\Feature\Member;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SelectMemberTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'test']);

        User::factory(2)->create();

        $this->actingAs($this->user);
    }

    public function test_user_can_select_a_member_to_chat_with()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);
        $group->members()->attach([2, 3]);

        $user2 = User::find(2);

        $this->get("groups/$group->slug/member/$user2->slug")
            ->assertInertia(function (AssertableInertia $page) use ($user2) {
                $page
                    ->component('Member/Show')
                    ->has('group')
                    ->has('member', function ($page) use ($user2) {
                        $page
                            ->where('id', $user2->id)
                            ->where('slug', $user2->slug)
                            ->etc();
                    });
            });
    }

    public function test_user_cannot_select_himself_to_chat_with()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);

        $this->get("groups/$group->slug/member/test")
            ->assertRedirect("groups/$group->slug");
    }

    public function test_when_user_selects_a_member_the_related_messages_are_returned()
    {
        $group = Group::factory()->create(['user_id' => $this->user->id]);
        $group->members()->attach([2, 3]);

        $user2 = User::find(2);
        $user3 = User::find(3);

        Message::factory()->create([
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $user2->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        Message::factory()->create([
            'group_id' => $group->id,
            'from' => 2,
            'receiver_id' => $this->user->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        Message::factory()->create([
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $user3->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->get("groups/$group->slug/member/$user2->slug")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->has('messages', 2);
            });
    }
}
