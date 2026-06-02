<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    // protected $fillable = ['name', 'cover_image'];
    protected $fillable = ['name', 'cover_image', 'description'];

    // Relasi ke tabel workspace_members (pivot)
    public function members()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    // // Relasi ke tabel collaborative_tasks
    // public function collaborativeTasks()
    // {
    //     return $this->hasMany(CollaborativeTask::class);
    // }

    // // Relasi ke tabel collaborative_schedules
    // public function collaborativeSchedules()
    // {
    //     return $this->hasMany(CollaborativeSchedule::class);
    // }
}
