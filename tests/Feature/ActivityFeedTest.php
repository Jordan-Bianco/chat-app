<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_all_activities_for_a_group()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->patch("/groups/$group->id", [
            'name' => 'updated',
            'description' => $group->description
        ]);

        $slug = $group->fresh()->slug;

        $this->get("/groups/$slug/activity-feed")
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Group/Activity')
                    ->has('activities', 2)
                    ->has('group');
            });
    }

    public function test_cannot_view_others_group_activities()
    {
        $user = $this->user();
        $user2 = $this->user();

        $group = Group::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user);

        $this->get("/groups/$group->slug/activity-feed")
            ->assertForbidden();
    }

    public function test_create_group_records_an_activity()
    {
        $user = $this->user();
        $this->actingAs($user);

        $this->post('/groups', [
            'name' => 'test',
            'description' => 'test description'
        ]);

        $group = Group::first();

        $this->assertEquals('group_created', $group->activities()->first()->name);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_created',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group'
        ]);
    }

    public function test_update_group_records_an_activity()
    {
        $user = $this->user();
        $this->actingAs($user);

        $this->post('/groups', [
            'name' => 'test',
            'description' => 'test description'
        ]);

        $group = Group::first();

        $this->patch("/groups/$group->id", [
            'name' => 'updated',
            'description' => 'updated'
        ]);

        $this->assertEquals('group_created', $group->activities()->first()->name);
        $this->assertEquals('group_updated', $group->activities()->find(2)->name);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_updated',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group'
        ]);
    }

    public function test_remove_member_from_group_generates_activity()
    {
        $user = $this->user();
        $user2 = $this->user();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach(2);

        $this->delete("/groups/$group->id/remove-member/2");

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_created',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'member_removed',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }

    public function test_promoting_a_member_to_leader_generates_activity()
    {
        $user = $this->user();
        $user2 = $this->user();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach(2);

        $this->patch("/groups/$group->id/member/$user2->id", [
            'role' => 'Leader'
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_created',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'member_promoted',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }

    public function test_demoting_a_member_to_user_generates_activity()
    {
        $user = $this->user();
        $user2 = $this->user();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach(2, ['role' => 'Leader']);

        $this->patch("/groups/$group->id/member/$user2->id", [
            'role' => 'User'
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_created',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'member_demoted',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }

    public function test_leaving_a_group_generates_activity()
    {
        $user = $this->user();
        $user2 = $this->user();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach(2);

        $this->actingAs($user2);
        $this->delete("/groups/$group->id/leave");

        $this->assertDatabaseHas('activities', [
            'author_id' => $user->id,
            'name' => 'group_created',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);

        $this->assertDatabaseHas('activities', [
            'author_id' => $user2->id,
            'name' => 'member_left',
            'subject_id' => Group::first()->id,
            'subject_type' => 'App\Models\Group',
        ]);
    }
}
