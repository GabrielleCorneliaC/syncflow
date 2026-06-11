<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [
        'collaborative_task_id',
        'user_id',
        'comment',
        'attachment_path'
    ];

    public function task()
    {
        return $this->belongsTo(
            CollaborativeTask::class,
            'collaborative_task_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
