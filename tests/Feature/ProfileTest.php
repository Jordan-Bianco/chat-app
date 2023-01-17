<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_users_profile()
    {
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_return_profile_component()
    {
        $user = $this->user();
        $this->actingAs($user);

        $response = $this->get("/profile");

        $response
            ->assertInertia(function (AssertableInertia $page) use ($user) {
                $page
                    ->component('Profile/Index')
                    ->has('user', function ($page) use ($user) {
                        $page
                            ->where('id', $user->id)
                            ->etc();
                    });
            });
    }

    public function test_return_my_sent_requests()
    {
        $user = $this->user();
        $this->actingAs($user);

        $group = Group::factory()->create();

        $this->post("/groups/$group->id/join");

        $response = $this->get("/profile/requests-sent");

        $response
            ->assertInertia(function (AssertableInertia $page) {
                $page
                    ->component('Profile/RequestsSent')
                    ->has('user')
                    ->has('requests', 1);
            });
    }
}
