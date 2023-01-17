<?php

namespace App\Http\Controllers;

use App\Events\SearchUser;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSearchController extends Controller
{
    public function liveSearch(Group $group, Request $request)
    {
        $users = DB::table('users')
            ->select('*')
            ->whereRaw("name NOT IN (SELECT name FROM users INNER JOIN group_user ON users.id = group_user.user_id WHERE group_user.group_id = '$group->id')")
            ->whereRaw("users.id NOT IN (SELECT `to` FROM join_requests WHERE `from` = '$group->id')")
            ->where('name', 'LIKE', "%$request->search%")
            ->limit(5)
            ->get();

        broadcast(new SearchUser($users));
    }
}
