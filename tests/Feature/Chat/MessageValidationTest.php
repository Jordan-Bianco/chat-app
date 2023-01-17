<?php

namespace Tests\Feature\Chat;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MessageValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_body_is_required_if_attachment_is_not_present()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => null
        ])
            ->assertSessionHasErrors('body');
    }

    public function test_attachment_is_required_if_body_is_not_present()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => ''
        ])
            ->assertSessionHasErrors('attachment');
    }

    public function test_attachment_is_not_required_if_body_is_present()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => 'test',
            'attachment' => null
        ])
            ->assertSessionHasNoErrors('body')
            ->assertSessionHasNoErrors('attachment');
    }

    public function test_body_is_not_required_if_attachment_is_present()
    {
        $user = $this->user();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->create('image.jpg', 70);

        $this->post('/messages', [
            'group_id' => $group->id,
            'from' => 1,
            'receiver_id' => $group->id,
            'receiver_type' => 'App\Models\Group',
            'body' => '',
            'attachment' => $image
        ])
            ->assertSessionHasNoErrors('body')
            ->assertSessionHasNoErrors('attachment');
    }
}
