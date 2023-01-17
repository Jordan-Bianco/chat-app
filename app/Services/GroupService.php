<?php

namespace App\Services;

use App\Models\Group;

class GroupService
{
    /**
     * @param Group $group
     * @param int $id
     * @param null|string $role
     * @return void
     */
    public function addMember(Group $group, int $id, string $role = 'User'): void
    {
        $group->members()->attach($id, ['role' => $role]);
    }
}
