<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
