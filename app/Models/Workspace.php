<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['name', 'cover_image'];

    // Relasi ke tabel workspace_members (pivot)
    public function members()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    public function collaborativeTasks()
    {
        return $this->hasMany(CollaborativeTask::class);
    }

    // // Relasi ke tabel collaborative_schedules
    // public function collaborativeSchedules()
    // {
    //     return $this->hasMany(CollaborativeSchedule::class);
    // }

    // // Relasi ke tabel resource_links
    // public function resourceLinks()
    // {
    //     return $this->hasMany(ResourceLink::class);
    // }
}
