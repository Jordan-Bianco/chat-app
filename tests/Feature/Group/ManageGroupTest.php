<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_new_group()
    {
        $this->actingAs($this->user());

        $this->post('/groups', [
            'name' => 'test',
            'description' => 'test description'
        ])
            ->assertSessionHas('message', 'Group test created!')
            ->assertRedirect('groups/test');

        $this->assertDatabaseHas('groups', [
            'user_id' => 1,
            'name' => 'test',
            'description' => 'test description'
        ]);
    }

    public function test_new_group_name_must_contain_at_least_one_letter_or_one_number()
    {
        $this->actingAs($this->user());

        $this->post('/groups', [
            'name' => '-',
            'description' => 'test description'
        ])
            ->assertSessionHasErrors('name');

        $this->post('/groups', [
            'name' => 12,
            'description' => 'test description'
        ])
            ->assertSessionHasNoErrors('name');

        $this->post('/groups', [
            'name' => 'test',
            'description' => 'test description'
        ])
            ->assertSessionHasNoErrors('name');
    }

    public function test_new_group_name_must_be_a_maximum_of_30_char_long()
    {
        $this->actingAs($this->user());

        $this->post('/groups', [
            'name' => 'lorem lorem lorem lorem lorem l',
            'description' => 'test description'
        ])
            ->assertSessionHasErrors('name');
    }

    public function test_if_user_creates_the_group_he_his_added_to_the_group_as_leader()
    {
        $user = $this->user();
        $this->actingAs($user);

        $this->post('/groups', [
            'name' => 'test',
            'description' => 'test description'
        ]);

        $this->assertTrue(Group::first()->members->contains($user->id));
        $this->assertEquals('Leader', $user->memberOf()->where('name', 'test')->first()->pivot->role);
    }

    public function test_team_leaders_can_edit_group()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->patch("/groups/$group->id", [
            'name' => 'updated',
            'description' => 'updated'
        ])
            ->assertSessionHas('message', 'Group updated updated!')
            ->assertRedirect('/groups/updated/settings/info');

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'user_id' => $user->id,
            'name' => 'updated',
            'description' => 'updated'
        ]);
    }

    public function test_when_group_name_is_updated_the_slug_is_updated_too()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->patch("/groups/$group->id", [
            'name' => 'updated',
            'description' => 'updated'
        ]);

        $this->assertEquals('updated', $group->fresh()->name);
        $this->assertEquals('updated', $group->fresh()->slug);
    }

    public function test_team_leaders_can_delete_group()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->delete("/groups/$group->id")
            ->assertSessionHas('message', "Group $group->name deleted!")
            ->assertRedirect('/');

        $this->assertDatabaseMissing('groups', $group->only('id'));
    }

    public function test_when_group_is_deleted_all_members_are_removed()
    {
        $user = $this->user();
        $this->actingAs($user);

        User::factory(2)->create();

        $group = Group::factory()->create(['user_id' => $user->id]);
        $group->members()->attach([2, 3]);

        $this->assertDatabaseCount('group_user', 3);

        $this->delete("/groups/$group->id");

        $this->assertDatabaseCount('group_user', 0);
    }

    public function test_when_group_is_deleted_all_messages_are_deleted_from_db()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        Message::factory()->create([
            'group_id' => $group->id,
            'from' => $user->id,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $this->assertEquals(1, $group->messages->count());
        $this->assertDatabaseCount('messages', 1);

        $this->delete("/groups/$group->id");

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_when_group_is_deleted_all_requests_and_invitations_are_deleted_from_db()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        User::factory(2)->create();

        $this->post("/groups/$group->id/invite-members", [
            'to' => 2
        ]);

        $this->actingAs(User::find(3));
        $this->post("/groups/$group->id/join");

        $this->assertDatabaseCount('join_requests', 2);

        $this->actingAs($leader);
        $this->delete("/groups/$group->id");

        $this->assertDatabaseCount('join_requests', 0);
    }

    public function test_when_group_is_deleted_all_unread_message_are_deleted()
    {
        $leader = $this->user();
        $this->actingAs($leader);

        $group = Group::factory()->create(['user_id' => $leader->id]);

        User::factory()->create();

        $group->members()->attach(2);

        $this->post('/messages', $message = [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => User::find(2)->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $message = Message::first();

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => $message->id,
            'from' => 1,
            'is_private' => true
        ]);

        $this->delete("/groups/$group->id");

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => $message->id,
            'from' => 1,
            'is_private' => true
        ]);
    }
}
