<?php

namespace App\Http\Middleware;

use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed[]
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),

                'groups' => $request->user() ?
                    $request->user()->memberOf
                    ->loadCount(['unreadGroupMessages' => function ($query) {
                        $query->where('user_id', auth()->id());
                    }])
                    ->loadCount(['unreadPrivateMessages' => function ($query) {
                        $query->where('user_id', auth()->id());
                    }])


                    : [],

                'invitations' => JoinRequest::query()
                    ->where('to', auth()->id())
                    ->where('from', '!=', auth()->id())
                    ->where('status', 'Pending')
                    ->get(),
            ],

            'flash' => [
                'message' => $request->session()->get('message')
            ],

            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
