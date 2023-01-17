<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Support\Str;

trait RecordActivity
{
    public static function bootRecordActivity()
    {
        static::created(function ($model) {
            Activity::create([
                'author_id' => $model->user_id,
                'name' => Str::lower(class_basename($model)) . '_created',
                'subject_id' => $model->id,
                'subject_type' => get_class($model),
                'data' => $model
            ]);
        });

        static::updated(function ($model) {

            Activity::create([
                'author_id' => $model->user_id,
                'name' => Str::lower(class_basename($model)) . '_updated',
                'subject_id' => $model->id,
                'subject_type' => get_class($model),
                'data' => json_encode([
                    'before' => [
                        'name' => $model->original['name'],
                        'description' => $model->original['description']
                    ],
                    'after' => [
                        'name' => $model->getChanges()['name'] ?? '',
                        'description' => $model->getChanges()['description'] ?? '',
                    ]
                ])
            ]);
        });
    }
}
