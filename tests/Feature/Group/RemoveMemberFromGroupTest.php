<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RemoveMemberFromGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_remove_member_from_group()
    {
        $this->delete("/groups/1/remove-member/1")->assertStatus(302);
    }

    public function test_leader_can_search_among_the_members()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        User::factory()->create(['name' => 'jane']);
        User::factory()->create(['name' => 'john']);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $group->members()->attach(2);

        $response = $this->get("/groups/$group->slug/settings/remove-members?search=jane");

        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->has('members.data', 1)
                ->has('members.links')
                ->has('members.data.0', function ($page) {
                    $page
                        ->where('id', 2)
                        ->where('name', 'jane')
                        ->etc();
                });
        });
    }

    public function test_admin_can_remove_member_from_group()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        User::factory(2)->create();

        $group = Group::factory()->create(['user_id' => $leader->id]);

        $group->members()->attach([2, 3]);

        $this->assertEquals(3, $group->members->count());

        $this->delete("/groups/$group->id/remove-member/2");

        $this->assertEquals(2, $group->fresh()->members->count());

        $this->assertDatabaseMissing('group_user', [
            'group_id' => $group->id,
            'user_id' => 2
        ]);
    }
}
