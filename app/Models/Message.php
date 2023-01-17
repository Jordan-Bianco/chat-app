<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'from');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function isUnread()
    {
        return $this->hasOne(UnreadMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
