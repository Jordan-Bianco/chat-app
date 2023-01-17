<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;

class ProfileController extends Controller
{
    public function index()
    {
        return inertia('Profile/Index', [
            'user' => auth()->user()
        ]);
    }

    public function requestsSent()
    {
        $requests = JoinRequest::query()
            ->where('from', auth()->id())
            ->join('groups', 'groups.id', '=', 'join_requests.to')
            ->get();

        return inertia('Profile/RequestsSent', [
            'user' => auth()->user(),
            'requests' => $requests
        ]);
    }

    public function invitations()
    {
        $invitations = JoinRequest::query()
            ->where('to', auth()->id())
            ->where('status', 'Pending')
            ->join('groups', 'groups.id', '=', 'join_requests.from')
            ->paginate(5);

        return inertia('Profile/Invitations', [
            'user' => auth()->user(),
            'invitations' => $invitations
        ]);
    }
}
