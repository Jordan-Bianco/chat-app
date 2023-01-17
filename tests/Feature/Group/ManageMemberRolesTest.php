<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageMemberRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_change_member_role()
    {
        $this->patch("/groups/1/member/1", ['role' => 'Leader'])->assertStatus(302);
    }

    public function test_leader_can_change_member_role()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $user = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $group->members()->attach($user->id);

        $this->assertEquals('User', $group->members->firstWhere('id', 2)->pivot->role);

        $this->patch("/groups/$group->id/member/$user->id", [
            'role' => 'Leader'
        ]);

        $this->assertEquals('Leader', $group->fresh()->members->firstWhere('id', 2)->pivot->role);
    }

    public function test_non_user_cannot_change_member_role()
    {
        $leader = $this->user();
        $user = $this->user();

        $group = Group::factory()->create(['user_id' => $leader->id]);
        $group->members()->attach(2);

        $this->actingAs($user);

        $this->patch("/groups/$group->id/member/$user->id", ['role' => 'Leader'])
            ->assertStatus(403);
    }
}
