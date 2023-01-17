<?php

namespace Tests\Feature\Chat;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_read_a_group_message_by_selecting_the_group()
    {
        $user = $this->user();
        User::factory(2)->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $message = Message::first();

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => $message->id,
            'is_private' => false
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 3,
            'message_id' => $message->id,
            'is_private' => false
        ]);

        $this->actingAs(User::find(2));

        $this->get("groups/$group->slug");

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => $message->id,
            'is_private' => false
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 3,
            'message_id' => $message->id,
            'is_private' => false
        ]);
    }

    public function test_user_can_mark_messages_as_read_if_he_is_already_in_the_selected_group_chat()
    {
        $user = $this->user();
        $user2 = User::factory()->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach(2);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $this->actingAs($user2);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 1,
            'is_private' => false
        ]);

        $this->delete("/unread-messages/$group->id");

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 1,
            'is_private' => false
        ]);
    }

    public function test_user_cannot_read_private_messages_by_selecting_the_group()
    {
        $user = $this->user();
        User::factory(2)->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

        /** User 1 send a message to the group */
        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 1,
            'is_private' => false
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 3,
            'message_id' => 1,
            'is_private' => false
        ]);

        /** User 1 send a private message to User 2 */
        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => User::find(2)->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 2,
            'is_private' => true
        ]);

        /** User 2 select the group */
        $this->actingAs(User::find(2));

        $this->get("groups/$group->slug");

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 1,
            'is_private' => false
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 2,
            'message_id' => 2,
            'is_private' => true
        ]);
    }

    public function test_user_can_read_private_messages_by_selecting_the_chat()
    {
        $user = $this->user();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

        /** User 1 send a message to User 2 */
        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $user2->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->actingAs($user3);

        /** User 3 send a message to User 2 */
        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 3,
            'receiver_id' => $user2->id,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => $user2->id,
            'message_id' => 1,
            'is_private' => true,
            'from' => $user->id
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => $user2->id,
            'message_id' => 2,
            'is_private' => true,
            'from' => $user3->id
        ]);

        $this->actingAs($user2);

        /** If User 2 select the chat with the User 1, the database should still have the unread message fromu User 3 */
        $this->get("groups/$group->slug/member/$user->slug");

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => $user2->id,
            'message_id' => 1,
            'is_private' => true,
            'from' => $user->id
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => $user2->id,
            'message_id' => 2,
            'is_private' => true,
            'from' => $user3->id
        ]);
    }

    public function test_user_can_mark_a_private_message_as_read_if_he_is_already_chatting_with_the_selected_user()
    {
        $user = $this->user();
        $user2 = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach(2);

        $this->actingAs($user2);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 2,
            'receiver_id' => 1,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('unread_messages', [
            'group_id' => $group->id,
            'user_id' => $user->id,
            'message_id' => 1,
            'is_private' => true,
            'from' => $user2->id
        ]);

        $this->actingAs($user);

        $this->delete("/unread-messages/$group->id/$user2->id");
    }
}
