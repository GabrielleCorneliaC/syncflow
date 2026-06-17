<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CollaborativeTask extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'deadline',
        'status',
        'progress',
        'attachments_count',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'progress' => 'integer',
            'attachments_count' => 'integer',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'collaborative_task_assignees')
            ->withPivot(['google_calendar_event_id', 'google_calendar_html_link'])
            ->withTimestamps();
    }

    public function calendarEndDate(): ?Carbon
    {
        return $this->deadline?->copy()->addDay();
    }
}
