<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborativeSchedule extends Model
{
    protected $fillable =
    [
        'workspace_id',
        'title',
        'description',
        'start_time',
        'end_time'
    ];
}
