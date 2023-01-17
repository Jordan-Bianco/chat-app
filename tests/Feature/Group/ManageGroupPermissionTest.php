<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageGroupPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_group()
    {
        $this->post('/groups', [])->assertStatus(302);
        $this->patch('/groups/1', [])->assertStatus(302);
        $this->delete('/groups/1', [])->assertStatus(302);
    }

    public function test_regular_users_cannot_edit_group()
    {
        $leader = $this->user();
        $member = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->actingAs($member);

        $this->patch("/groups/$group->id", [
            'name' => 'updated',
            'description' => 'updated'
        ])
            ->assertForbidden();
    }

    public function test_only_leaders_can_delete_group()
    {
        $leader = $this->user();
        $member = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $this->actingAs($member);

        $this->delete("/groups/$group->id")
            ->assertForbidden();
    }
}
