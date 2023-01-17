<?php

namespace Tests\Feature\Chat;

use App\Events\GroupMessageSent;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendGroupMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_sent_messages_are_stored_in_database()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/messages', $message = [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $this->assertDatabaseHas('messages', $message);
    }

    public function test_event_groupMessageSent_is_created()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Event::fake();

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test'
        ]);

        $message = Message::first();

        Event::assertDispatched(function (GroupMessageSent $event) use ($message) {
            return $event->message->receiver_type === $message->receiver_type;
        });
    }

    public function test_when_user_send_a_group_message_the_unread_messages_rows_are_stored()
    {
        $user = $this->user();
        User::factory()->create();

        $this->actingAs($user);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $group->members()->attach(2);

        $this->post('/messages', $message = [
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
    }

    public function test_user_can_send_image_attachment()
    {
        Storage::fake('public');

        $user = $this->user();
        $this->actingAs($user);

        $image = UploadedFile::fake()->create('image.jpg', 70);
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => $image
        ]);

        $message = Message::first();

        $this->assertDatabaseHas('attachments', [
            'message_id' => $message->id,
            'name' => $image->getClientOriginalName(),
            'extension' => $image->extension(),
            'mime_type' => $image->getMimeType(),
            'size' => $image->getSize()
        ]);

        Storage::disk('public')->assertExists("attachments/$group->id/$message->id/" . $image->getClientOriginalName());
    }

    public function test_same_name_attachment()
    {
        Storage::fake('public');

        $user = $this->user();

        $image = UploadedFile::fake()->create('image.jpg', 70);
        $image2 = UploadedFile::fake()->create('image.jpg', 100);

        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => $image
        ]);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => $image2
        ]);

        $this->assertDatabaseHas('attachments', [
            'message_id' => 1,
            'name' => $image->getClientOriginalName(),
            'extension' => $image->extension(),
            'mime_type' => $image->getMimeType(),
            'size' => $image->getSize()
        ]);

        $this->assertDatabaseHas('attachments', [
            'message_id' => 2,
            'name' => $image2->getClientOriginalName(),
            'extension' => $image2->extension(),
            'mime_type' => $image2->getMimeType(),
            'size' => $image2->getSize()
        ]);

        Storage::disk('public')->assertExists("attachments/$group->id/1/" . $image->getClientOriginalName());
        Storage::disk('public')->assertExists("attachments/$group->id/2/" . $image2->getClientOriginalName());
    }
}
