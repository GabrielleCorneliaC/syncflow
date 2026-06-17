<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_access_token' => 'encrypted',
            'google_refresh_token' => 'encrypted',
            'google_token_expires_at' => 'datetime',
        ];
    }

    public function personalTasks()
    {
        return $this->hasMany(PersonalTask::class);
    }

    public function personalSchedules()
    {
        return $this->hasMany(PersonalSchedule::class);
    }

    public function collaborativeTasks()
    {
        return $this->belongsToMany(CollaborativeTask::class, 'collaborative_task_assignees')
            ->withPivot(['google_calendar_event_id', 'google_calendar_html_link'])
            ->withTimestamps();
    }

    public function hasGoogleCalendarConnected(): bool
    {
        return filled($this->google_access_token) || filled($this->google_refresh_token);
    }
}
