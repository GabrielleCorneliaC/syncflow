<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PersonalSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'time',
        'end_time',
        'timezone',
        'location',
        'google_calendar_event_id',
        'google_calendar_html_link',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function startDateTime(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d').' '.$this->time, $this->timezone ?? 'Asia/Jakarta');
    }

    public function endDateTime(): Carbon
    {
        if ($this->end_time) {
            return Carbon::parse($this->date->format('Y-m-d').' '.$this->end_time, $this->timezone ?? 'Asia/Jakarta');
        }

        return $this->startDateTime()->copy()->addHour();
    }

    public function timezoneLabel(): string
    {
        return match ($this->timezone) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB',
        };
    }
}
