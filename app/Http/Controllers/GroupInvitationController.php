<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\JoinRequest;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupInvitationController extends Controller
{
    public function invite(Group $group, Request $request)
    {
        $invitation = JoinRequest::query()
            ->where('from', $group->id)
            ->where('to', $request->to)
            ->first();

        if (!$invitation) {
            JoinRequest::create([
                'from' => $group->id,
                'to' => $request->to,
                'status' => 'Pending'
            ]);

            return back()->with('message', 'Invitation sent!');
        } else {
            $invitation->delete();
        }
    }

    public function accept(Group $group, GroupService $groupService)
    {
        JoinRequest::query()
            ->where('from', $group->id)
            ->where('to', auth()->id())
            ->first()
            ->update([
                'status' => 'Accepted'
            ]);

        $groupService->addMember($group, auth()->id(), 'User');

        Activity::create([
            'author_id' => null,
            'name' => 'member_added',
            'subject_id' => $group->id,
            'subject_type' => get_class($group),
            'data' => json_encode(['user' => auth()->user()])
        ]);
    }

    public function reject(Group $group)
    {
        JoinRequest::query()
            ->where('from', $group->id)
            ->where('to', auth()->id())
            ->first()
            ->update([
                'status' => 'Rejected'
            ]);
    }
}
