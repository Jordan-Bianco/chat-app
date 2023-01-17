<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $date = [
        'created_at'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}
