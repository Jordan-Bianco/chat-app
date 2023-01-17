<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    use HasFactory;

    protected $date = [
        'created_at'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }
}
