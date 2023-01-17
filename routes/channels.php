<?php

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('search-user.{id}', function ($id) {
    return (int) auth()->id() === (int) $id;
});

Broadcast::channel('search-group.{id}', function ($id) {
    return (int) auth()->id() === (int) $id;
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('group.{id}', function ($user, $groupId) {

    $group = Group::find($groupId);

    if ($group->members->contains($user->id)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'slug' => $user->slug
        ];
    } else {
        return null;
    }
});

Broadcast::channel('my-groups.{id}', function ($id) {
    return auth()->check() && auth()->user()->memberOf->contains($id);
});
