<?php

namespace App\Http\Controllers;

use App\Events\SearchGroup;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupSearchController extends Controller
{
    public function liveSearch(Request $request)
    {
        $groups = Group::query()
            ->where('name', 'LIKE', "%$request->search%")
            ->withCount('members')
            ->limit(4)
            ->get();

        broadcast(new SearchGroup($groups));
    }

    public function index($search)
    {
        $groups = Group::query()
            ->where('name', 'LIKE', "%$search%")
            ->withCount('members')
            ->paginate(8);

        return inertia('Search', [
            'results' => GroupResource::collection($groups),
            'search' => $search
        ]);
    }
}
