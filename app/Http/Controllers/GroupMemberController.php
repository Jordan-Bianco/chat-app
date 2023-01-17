<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;

class GroupMemberController extends Controller
{
    public function show($gslug, $uslug)
    {
        $group = Group::firstWhere('slug', $gslug);
        $member = $group->members->firstWhere('slug', $uslug);

        if (auth()->id() === $member->id) {
            return redirect()->route('group.show', $group->slug);
        }

        $messages = Message::query()
            ->where('messages.group_id', $group->id)
            ->where(function ($query) use ($member) {
                $query
                    ->where('messages.from', auth()->id())
                    ->where('messages.receiver_id', $member->id);
            })
            ->orWhere(function ($query) use ($member) {
                $query
                    ->where('messages.receiver_id', auth()->id())
                    ->where('messages.from', $member->id);
            })
            ->with('sender', 'isUnread', 'attachments')
            ->orderBy('messages.created_at', 'asc')
            ->get();

        $this->readPrivateUnreadMessages($group, $member);
        $unreadGroupPrivateMessages = $this->getGroupUnreadPrivateMessages($group);

        return inertia('Member/Show', [
            'group' => $group,
            'member' => $member,
            'isMember' => $group->members->contains(auth()->id()),
            'messages' => $messages,
            'unreadGroupPrivateMessages' => $unreadGroupPrivateMessages
        ]);
    }

    public function destroy($groupId, $userId)
    {
        $group = Group::firstWhere('id', $groupId);
        $user = User::firstWhere('id', $userId);
        $group->members()->detach($userId);

        Activity::create([
            'author_id' => auth()->id(),
            'name' => 'member_removed',
            'subject_id' => $group->id,
            'subject_type' => get_class($group),
            'data' => json_encode(['user' => $user])
        ]);

        return back()->with('message', "Member removed from \"$group->name\"");
    }

    protected function readPrivateUnreadMessages($group, $member)
    {
        $group->unreadPrivateMessages()
            ->where('user_id', auth()->id())
            ->where('from', $member->id)
            ->delete();
    }

    protected function getGroupUnreadPrivateMessages($group)
    {
        return $group->unreadPrivateMessages()
            ->where('user_id', auth()->id())
            ->get();
    }
}
