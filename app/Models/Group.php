<?php

namespace App\Models;

use App\Traits\RecordActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, RecordActivity;

    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        // Create slug and uuid
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->id = Str::uuid();

            // Add the creator of the group as group leader
            $model->members()->attach($model->user_id, ['role' => 'Leader']);
        });

        // Update slug
        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'subject_id')->latest();
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps()
            ->using(GroupUser::class);
    }

    public function leaders()
    {
        return $this->members()->wherePivot('role', 'Leader')->get();
    }

    public function unreadPrivateMessages()
    {
        return $this->hasMany(UnreadMessage::class)->where('is_private', true);
    }

    public function unreadGroupMessages()
    {
        return $this->hasMany(UnreadMessage::class)->where('is_private', false);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'receiver')
            ->orderBy('created_at', 'asc');
    }
}
