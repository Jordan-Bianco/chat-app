<?php

namespace Tests\Feature\Chat;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_home_component()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Home');
            });
    }
}
