<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $group = Group::factory()->create(['user_id' => 1]);

        foreach (User::all() as $user) {
            Group::factory()->create(['user_id' => $user->id]);
        }

        $group->members()->attach([2, 4, 5, 8, 10]);
    }
}
