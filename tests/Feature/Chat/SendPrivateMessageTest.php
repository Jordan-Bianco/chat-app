<?php

namespace Tests\Feature\Chat;

use App\Events\PrivateMessageSent;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_sent_messages_are_stored_in_database()
    {
        $user = $this->user();
        User::factory(2)->create();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

        $this->actingAs(User::find(2));

        $this->post('/messages', $message = [
            'group_id' => $group->id,
            'from' => 2,
            'receiver_id' => 3,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('messages', $message);
    }

    public function test_event_privateMessageSent_is_created()
    {
        $user = $this->user();
        User::factory(2)->create();

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

        $this->actingAs(User::find(2));

        Event::fake();

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 2,
            'receiver_id' => 3,
            'receiver_type' => 'App\Models\User',
            'body' => 'test'
        ]);

        $message = Message::first();

        Event::assertDispatched(function (PrivateMessageSent $event) use ($message) {
            return $event->message->receiver_type === $message->receiver_type;
        });
    }

    public function test_when_user_send_a_private_message_the_unread_message_row_is_stored()
    {
        $user = $this->user();
        User::factory(2)->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach([2, 3]);

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

        $this->assertDatabaseMissing('unread_messages', [
            'group_id' => $group->id,
            'user_id' => 3,
            'message_id' => $message->id,
            'from' => 1,
            'is_private' => true
        ]);
    }
}
