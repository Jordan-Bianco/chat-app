<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Http\Request;

class GroupSettingController extends Controller
{
    public function info(Group $group)
    {
        $this->authorize('view', $group);

        $joinRequestsCount = $this->getPendingJoinRequests($group);

        return inertia('Group/Settings/EditGroupInfo', [
            'group' => $group,
            'joinRequestsCount' => $joinRequestsCount
        ]);
    }

    public function delete(Group $group)
    {
        $this->authorize('view', $group);

        $joinRequestsCount = $this->getPendingJoinRequests($group);

        return inertia('Group/Settings/DeleteGroup', [
            'group' => $group,
            'joinRequestsCount' => $joinRequestsCount
        ]);
    }

    public function manageRoles(Group $group, Request $request)
    {
        $this->authorize('view', $group);

        $joinRequestsCount = $this->getPendingJoinRequests($group);

        $members = User::query()
            ->withSearch($request->search ?? '')
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->where('group_id', $group->id)
            ->paginate(10);

        return inertia('Group/Settings/ManageRoles', [
            'group' => $group,
            'members' => $members,
            'joinRequestsCount' => $joinRequestsCount
        ]);
    }

    public function removeMembers(Group $group, Request $request)
    {
        $this->authorize('view', $group);

        $joinRequestsCount = $this->getPendingJoinRequests($group);

        $members = User::query()
            ->withSearch($request->search ?? '')
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->where('group_id', $group->id)
            ->paginate(10);

        return inertia('Group/Settings/RemoveMembers', [
            'group' => $group,
            'members' => $members,
            'joinRequestsCount' => $joinRequestsCount
        ]);
    }

    public function joinRequests(Group $group)
    {
        $this->authorize('view', $group);

        $joinRequests = JoinRequest::query()
            ->select('join_requests.*', 'users.id', 'users.name', 'users.slug')
            ->where('to', $group->id)
            ->where('status', 'Pending')
            ->join('users', 'users.id', '=', 'from')
            ->paginate(5);

        return inertia('Group/Settings/JoinRequests', [
            'group' => $group,
            'joinRequests' => $joinRequests
        ]);
    }

    public function inviteMembers(Group $group)
    {
        $this->authorize('view', $group);

        $joinRequestsCount = $this->getPendingJoinRequests($group);

        $invitations = JoinRequest::query()
            ->where('from', $group->id)
            ->where('status', 'Pending')
            ->join('users', 'users.id', '=', 'to')
            ->paginate(5);

        return inertia('Group/Settings/InviteMembers', [
            'group' => $group,
            'joinRequestsCount' => $joinRequestsCount,
            'invitations' => $invitations
        ]);
    }

    protected function getPendingJoinRequests($group)
    {
        return JoinRequest::query()
            ->where('to', $group->id)
            ->where('status', 'Pending')
            ->count();
    }
}
