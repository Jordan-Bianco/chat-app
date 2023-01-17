<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupActivityController extends Controller
{
    public function index(Group $group)
    {
        abort_if(!$group->members->contains(auth()->id()), 403);

        $activities = $group->activities
            ->load([
                'author:id,name,slug',
                'subject'
            ]);

        return inertia('Group/Activity', [
            'activities' => $activities,
            'group' => $group
        ]);
    }
}
