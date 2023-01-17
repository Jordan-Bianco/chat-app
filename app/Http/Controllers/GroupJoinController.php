<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\JoinRequest;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupJoinController extends Controller
{
    public function store(Group $group)
    {
        $joinRequest = JoinRequest::query()
            ->where('from', auth()->id())
            ->where('to', $group->id)
            ->first();

        if (!$joinRequest) {
            JoinRequest::create([
                'from' => auth()->id(),
                'to' => $group->id,
                'status' => 'Pending'
            ]);
        } else {
            $joinRequest->delete();
        }
    }

    public function status(Group $group)
    {
        $joinRequest = JoinRequest::query()
            ->where('from', auth()->id())
            ->where('to', $group->id)
            ->first();

        if ($joinRequest) {
            $status = $joinRequest->status;
        } else {
            $status = 'null';
        }

        return response()->json([
            'status' => $status
        ]);
    }

    public function reject(Group $group, Request $request)
    {
        JoinRequest::query()
            ->where('from', $request->from)
            ->where('to', $group->id)
            ->first()
            ->update([
                'status' => 'Rejected'
            ]);
    }

    public function accept(Group $group, Request $request, GroupService $groupService)
    {
        $user = User::find($request->from);

        JoinRequest::query()
            ->where('from', $request->from)
            ->where('to', $group->id)
            ->first()
            ->update([
                'status' => 'Accepted'
            ]);

        $groupService->addMember($group, $user->id, 'User');

        Activity::create([
            'author_id' => auth()->id(),
            'name' => 'member_added',
            'subject_id' => $group->id,
            'subject_type' => get_class($group),
            'data' => json_encode(['user' => $user])
        ]);
    }
}
