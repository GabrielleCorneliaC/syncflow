<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'role',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
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

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&background=b30084&color=fff&size=128&bold=true&format=svg';
    }

    public function isGoogleUser(): bool
    {
        return ! is_null($this->google_id);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
