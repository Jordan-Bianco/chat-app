<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Activity;
use App\Models\Group;
use App\Models\JoinRequest;
use App\Models\UnreadMessage;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function show(Group $group)
    {
        $messages = $group->messages->load('sender', 'attachments');
        $group = $group->load('members');
        $isMember = $group->members->contains(auth()->id());
        $isLeader = $group->leaders()->contains(auth()->id());
        $joinRequests = JoinRequest::query()->where('to', $group->id)->where('status', 'Pending')->get();

        $this->readPublicUnreadMessages($group);

        $unreadGroupPrivateMessages = $this->getGroupUnreadPrivateMessages($group);

        return inertia('Group/Show', [
            'group' => $group,
            'messages' => $messages,
            'isMember' => $isMember,
            'isLeader' => $isLeader,
            'joinRequests' => $joinRequests,
            'unreadGroupPrivateMessages' => $unreadGroupPrivateMessages
        ]);
    }

    public function store(GroupRequest $request)
    {
        $validated = $request->validated();

        $group = auth()->user()->groups()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('group.show', $group->slug)
            ->with('message', "Group $group->name created!");
    }

    public function update(GroupRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validated();

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('group.settings.info', $group->fresh()->slug)
            ->with('message', "Group $group->name updated!");
    }

    public function destroy(Group $group)
    {
        $this->authorize('update', $group);

        DB::transaction(function () use ($group) {
            $group->members()->detach();

            $group->messages()->delete();

            JoinRequest::query()
                ->where('from', $group->id)
                ->orWhere('to', $group->id)
                ->delete();


            $group->unreadGroupMessages()->delete();
            $group->unreadPrivateMessages()->delete();
            $group->messages()->delete();

            $group->delete();
        });

        return redirect()
            ->route('home')
            ->with('message', "Group $group->name deleted!");
    }

    public function leave(Group $group)
    {
        $this->checkLeaderLeft($group);

        $group->members()->detach(auth()->id());

        if ($group->fresh()->members->count() === 0) {
            $group->delete();
        } else {
            Activity::create([
                'author_id' => auth()->id(),
                'name' => 'member_left',
                'subject_id' => $group->id,
                'subject_type' => get_class($group),
                'data' => json_encode(['user' => auth()->user()])
            ]);
        }

        return redirect()
            ->route('home')
            ->with('message', "You left the group $group->name!");
    }

    protected function checkLeaderLeft($group)
    {
        $role = $group->members()->firstWhere('id', auth()->id())->pivot->role;

        if ($role === 'Leader') {

            if (
                $group->leaders()->except(auth()->id())->count() === 0
                && $group->members->except(auth()->id())->count() >= 1
            ) {

                $group->members
                    ->except(auth()->id())
                    ->random()
                    ->pivot
                    ->update([
                        'role' => 'Leader'
                    ]);
            }
        }
    }

    protected function readPublicUnreadMessages($group)
    {
        $group->unreadGroupMessages()
            ->where('user_id', auth()->id())
            ->delete();
    }

    protected function getGroupUnreadPrivateMessages($group)
    {
        return $group->unreadPrivateMessages()
            ->where('user_id', auth()->id())
            ->get();
    }
}
