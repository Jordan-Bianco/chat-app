<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_leave_the_group()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach($user2->id);

        $this->assertDatabaseHas('group_user', [
            'user_id' => 1,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('group_user', [
            'user_id' => 2,
            'group_id' => $group->id
        ]);

        $this->actingAs($user2);

        $this->delete("/groups/$group->id/leave");

        $this->assertDatabaseHas('group_user', [
            'user_id' => 1,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseMissing('group_user', [
            'user_id' => 2,
            'group_id' => $group->id
        ]);
    }

    public function test_if_the_leader_of_the_group_leaves_the_group_and_he_is_the_only_member_the_group_is_eliminated()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id
        ]);

        $this->actingAs($user);

        $this->delete("/groups/$group->id/leave");

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id
        ]);
    }

    public function test_if_leader_leaves_the_group_and_there_arent_other_leaders_a_random_member_became_leader()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach($user2->id);

        $this->assertDatabaseHas('group_user', [
            'user_id' => 1,
            'group_id' => $group->id,
            'role' => 'Leader'
        ]);

        $this->assertDatabaseHas('group_user', [
            'user_id' => 2,
            'group_id' => $group->id,
            'role' => 'User'
        ]);

        $this->actingAs($user);

        $this->delete("/groups/$group->id/leave");

        $this->assertDatabaseMissing('group_user', [
            'user_id' => 1,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('group_user', [
            'user_id' => 2,
            'group_id' => $group->id,
            'role' => 'Leader'
        ]);
    }
}
