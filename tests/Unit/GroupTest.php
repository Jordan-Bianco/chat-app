<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_is_created_by_user()
    {
        $group = Group::factory()->create();

        $this->assertInstanceOf(User::class, $group->user);
    }

    public function test_group_can_have_many_members()
    {
        $group = Group::factory()->create();

        $this->assertInstanceOf(Collection::class, $group->members);
    }

    public function test_group_can_have_many_activities()
    {
        $group = Group::factory()->create();

        $this->assertInstanceOf(Collection::class, $group->activities);
    }

    public function test_addMember_function_add_user_to_group()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $groupService = new GroupService();

        $groupService->addMember($group, $user->id);

        $this->assertEquals(2, $group->members->count());
    }

    public function test_leaders_function_return_all_group_leaders()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->assertEquals(1, $group->leaders()->count());
    }
}
