<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageSent;
use App\Events\PrivateMessageSent;
use App\Http\Requests\MessageRequest;
use App\Models\Group;
use App\Models\Message;
use App\Models\UnreadMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function store(MessageRequest $request)
    {
        $validated = $request->validated();

        $file = $request->attachment;
        $receiverType = str_contains($request->receiver_id, '-') ? 'App\Models\Group' : 'App\Models\User';
        $group = Group::firstWhere('id', $request->group_id);

        $message = DB::transaction(function () use ($file, $receiverType, $request, $validated, $group) {
            $message = Message::create([
                'group_id' => $group->id,
                'from' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'receiver_type' => $receiverType,
                'body' => $validated['body'],
            ]);

            if ($file) {
                $this->storeAttachment($file, $message);
            }

            return $message;
        });

        if ($receiverType === 'App\Models\User') {
            broadcast(new PrivateMessageSent($message->load('sender', 'attachments')))->toOthers();

            UnreadMessage::create([
                'group_id' => $group->id,
                'user_id' => $message->receiver_id,
                'message_id' => $message->id,
                'from' => $message->sender->id,
                'is_private' => true
            ]);
        } else {
            broadcast(new GroupMessageSent($message->load('sender', 'attachments')))->toOthers();

            $group->members->except($message->from)->each(function ($member) use ($message, $group) {
                UnreadMessage::create([
                    'group_id' => $group->id,
                    'user_id' => $member->id,
                    'message_id' => $message->id,
                    'is_private' => false
                ]);
            });
        }
    }

    public function destroy($id)
    {
        UnreadMessage::query()
            ->where('is_private', false)
            ->where('group_id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function destroyPrivate($groupId, $userId)
    {
        UnreadMessage::query()
            ->where('is_private', true)
            ->where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('from', $userId)
            ->delete();
    }

    protected function storeAttachment($file, $message)
    {
        $name = trim($file->getClientOriginalName());
        $name = stripslashes($name);
        $name = htmlspecialchars($name);

        $message->attachments()->create([
            'name' => $name,
            'extension' => $file->extension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        Storage::putFileAs("attachments/$message->group_id/$message->id", $file, $name, 'public');
    }
}
