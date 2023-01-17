<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupMemberRoleController extends Controller
{
    public function update($groupId, $userId, Request $request)
    {
        $group = Group::firstWhere('id', $groupId);

        $this->authorize('update', $group);

        $user = $group->members->firstWhere('id', $userId);

        $user->pivot->update([
            'role' => $request->role
        ]);

        Activity::create([
            'author_id' => auth()->id(),
            'name' => $request->role === 'Leader' ? 'member_promoted' : 'member_demoted',
            'subject_id' => $group->id,
            'subject_type' => get_class($group),
            'data' => json_encode(['user' => $user])
        ]);
    }
}
