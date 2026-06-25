<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['name', 'cover_image', 'description'];

    public function members()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    public function collaborativeTasks()
    {
        return $this->hasMany(CollaborativeTask::class);
    }

    public function collaborativeSchedules()
    {
        return $this->hasMany(CollaborativeSchedule::class);
    }

    public function resourceLinks()
    {
        return $this->hasMany(ResourceLink::class);
    }
}
