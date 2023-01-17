<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name, '_');
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeWithSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%");
        });
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'receiver');
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function memberOf()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps()
            ->using(GroupUser::class)
            ->latest();
    }
}
