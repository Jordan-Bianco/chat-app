<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_group()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->groups);
    }

    public function test_user_can_belongs_to_many_groups()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->memberOf);
    }
}
