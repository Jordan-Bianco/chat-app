<?php

use App\Http\Controllers\GroupActivityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInvitationController;
use App\Http\Controllers\GroupJoinController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\GroupMemberRoleController;
use App\Http\Controllers\GroupSearchController;
use App\Http\Controllers\GroupSettingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::post('/messages', [MessageController::class, 'store'])->name('message.store');
    Route::delete('/unread-messages/{id}', [MessageController::class, 'destroy'])->name('message.destroy');
    Route::delete('/unread-messages/{groupId}/{userId}', [MessageController::class, 'destroyPrivate'])->name('message.destroy.private');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/requests-sent', [ProfileController::class, 'requestsSent'])->name('profile.requests-sent');
    Route::get('/profile/invitations', [ProfileController::class, 'invitations'])->name('profile.invitations');

    // Group
    Route::prefix('groups')->group(function () {

        // Manage Group
        Route::get('/{group:slug}', [GroupController::class, 'show'])->name('group.show');
        Route::post('/', [GroupController::class, 'store'])->name('group.store');
        Route::patch('/{group:id}', [GroupController::class, 'update'])->name('group.update');
        Route::delete('/{group:id}', [GroupController::class, 'destroy'])->name('group.destroy');

        // Manage Group members
        Route::delete('/{groupId}/remove-member/{userId}', [GroupMemberController::class, 'destroy'])->name('group.member.destroy');
        // Manage Group members role
        Route::patch('/{groupId}/member/{userId}', [GroupMemberRoleController::class, 'update'])->name('group.member.role.update');

        // Group Search
        Route::get('/', [GroupSearchController::class, 'liveSearch'])->name('group.search.live');
        Route::get('/search/{search}', [GroupSearchController::class, 'index'])->name('group.search.index');

        // Show single member to chat with
        Route::get('/{gslug}/member/{uslug}', [GroupMemberController::class, 'show'])->name('group.member.show');

        // Group Settings
        Route::get('/{group:slug}/settings/info', [GroupSettingController::class, 'info'])->name('group.settings.info');
        Route::get('/{group:slug}/settings/delete', [GroupSettingController::class, 'delete'])->name('group.settings.delete');
        Route::get('/{group:slug}/settings/remove-members', [GroupSettingController::class, 'removeMembers'])->name('group.settings.remove-members');
        Route::get('/{group:slug}/settings/manage-roles', [GroupSettingController::class, 'manageRoles'])->name('group.settings.manage-roles');
        Route::get('/{group:slug}/settings/join-requests', [GroupSettingController::class, 'joinRequests'])->name('group.settings.join-requests');
        Route::get('/{group:slug}/settings/invite-members', [GroupSettingController::class, 'inviteMembers'])->name('group.settings.invite-members');

        // Group activities
        Route::get('/{group:slug}/activity-feed', [GroupActivityController::class, 'index'])->name('group.activity');

        // Group join requests
        Route::get('/{group:id}/request-status', [GroupJoinController::class, 'status'])->name('group.join.status');
        Route::post('/{group:id}/join', [GroupJoinController::class, 'store'])->name('group.join.store');
        Route::post('/{group:id}/reject-request', [GroupJoinController::class, 'reject'])->name('group.join.reject');
        Route::post('/{group:id}/accept-request', [GroupJoinController::class, 'accept'])->name('group.join.accept');

        // Invitations
        Route::post('/{group:id}/accept-invitation', [GroupInvitationController::class, 'accept'])->name('group.invitation.accept');
        Route::post('/{group:id}/reject-invitation', [GroupInvitationController::class, 'reject'])->name('group.invitation.reject');
        Route::post('/{group:id}/invite-members', [GroupInvitationController::class, 'invite'])->name('group.invite-user');
        // Users Search
        Route::get('/{group:id}/users', [UserSearchController::class, 'liveSearch'])->name('user.search.live');

        // Group leave
        Route::delete('/{group:id}/leave', [GroupController::class, 'leave'])->name('group.leave');
    });
});

require __DIR__ . '/auth.php';
