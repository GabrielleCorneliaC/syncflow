<?php

namespace App\Services;

use App\Models\CollaborativeSchedule;
use App\Models\CollaborativeTask;
use App\Models\PersonalSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    public function authorizationUrl(): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/calendar.events',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);
    }

    public function storeTokensFromCallback(User $user, string $code): void
    {
        $response = Http::asForm()
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => $this->redirectUri(),
                'grant_type' => 'authorization_code',
            ])
            ->throw()
            ->json();

        $user->forceFill([
            'google_access_token' => $response['access_token'] ?? null,
            'google_refresh_token' => $response['refresh_token'] ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($response['expires_in'] ?? 3600),
        ])->save();
    }

    public function syncSchedule(PersonalSchedule $schedule): void
    {
        $user = $schedule->user;

        if (! $user || ! $user->hasGoogleCalendarConnected()) {
            return;
        }

        $payload = $this->eventPayload($schedule);
        $token = $this->validAccessToken($user);

        if ($schedule->google_calendar_event_id) {
            $response = Http::withToken($token)
                ->withOptions(['verify' => config('services.google.guzzle.verify')])
                ->put(
                    "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$schedule->google_calendar_event_id}",
                    $payload
                )
                ->throw()
                ->json();
        } else {
            $response = Http::withToken($token)
                ->withOptions(['verify' => config('services.google.guzzle.verify')])
                ->post(
                    'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                    $payload
                )
                ->throw()
                ->json();
        }

        $schedule->forceFill([
            'google_calendar_event_id' => $response['id'] ?? $schedule->google_calendar_event_id,
            'google_calendar_html_link' => $response['htmlLink'] ?? $schedule->google_calendar_html_link,
        ])->save();
    }

    public function deleteSchedule(PersonalSchedule $schedule): void
    {
        $user = $schedule->user;

        if (! $user || ! $user->hasGoogleCalendarConnected() || ! $schedule->google_calendar_event_id) {
            return;
        }

        Http::withToken($this->validAccessToken($user))
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->delete(
                "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$schedule->google_calendar_event_id}"
            )
            ->throw();
    }


    public function syncCollaborativeTask(CollaborativeTask $task, User $user): ?array
    {
        if (! $task->deadline || ! $user->hasGoogleCalendarConnected()) {
            return null;
        }

        $task->loadMissing('assignees');

        $payload = $this->collaborativeTaskPayload($task);
        $token = $this->validAccessToken($user);

        $eventId = $task->assignees
            ->firstWhere('id', $user->id)
            ?->pivot
            ?->google_calendar_event_id;

        if ($eventId) {
            return Http::withToken($token)
                ->withOptions(['verify' => config('services.google.guzzle.verify')])
                ->put(
                    "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}",
                    $payload
                )
                ->throw()
                ->json();
        }

        return Http::withToken($token)
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->post(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                $payload
            )
            ->throw()
            ->json();
    }

    public function deleteCollaborativeTask(CollaborativeTask $task, User $user): void
    {
        $task->loadMissing('assignees');

        $eventId = $task->assignees
            ->firstWhere('id', $user->id)
            ?->pivot
            ?->google_calendar_event_id;

        if (! $eventId || ! $user->hasGoogleCalendarConnected()) {
            return;
        }

        Http::withToken($this->validAccessToken($user))
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->delete(
                "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}"
            )
            ->throw();
    }


    public function syncCollaborativeSchedule(CollaborativeSchedule $schedule, User $user): ?array
    {
        if (! $user || ! $user->hasGoogleCalendarConnected()) {
            return null;
        }

        $payload = $this->collaborativeSchedulePayload($schedule);
        $token = $this->validAccessToken($user);

        if ($schedule->google_calendar_event_id) {
            return Http::withToken($token)
                ->withOptions(['verify' => config('services.google.guzzle.verify')])
                ->put(
                    "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$schedule->google_calendar_event_id}",
                    $payload
                )
                ->throw()
                ->json();
        }

        $response = Http::withToken($token)
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->post(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                $payload
            )
            ->throw()
            ->json();

        if (isset($response['id'])) {
            $schedule->forceFill([
                'google_calendar_event_id' => $response['id'],
            ])->save();
        }

        return $response;
    }



    private function collaborativeTaskPayload(CollaborativeTask $task): array
{
    $timezone = 'Asia/Jakarta';

    $deadline = Carbon::createFromFormat(
    'Y-m-d H:i:s',
    $task->getRawOriginal('deadline'),
    $timezone
);
    $description = trim(
        ($task->description ? $task->description . "\n\n" : '') .
        "Status: {$task->status}\n" .
        "Progress: {$task->progress}%"
    );

    $statusText = $task->status === 'done' ? '[DONE] ' : '[TODO] ';

    return [
        'summary' => $statusText . '[SyncFlow Task] ' . $task->name,
        'description' => $description,

        'start' => [
            'dateTime' => $deadline->format('Y-m-d\TH:i:s'),
            'timeZone' => $timezone,
        ],

 
        'end' => [
            'dateTime' => $deadline->copy()
                ->addMinutes(5)
                ->format('Y-m-d\TH:i:s'),
            'timeZone' => $timezone,
        ],

        'reminders' => [
            'useDefault' => true,
        ],
    ];
}
    private function collaborativeSchedulePayload(CollaborativeSchedule $schedule): array
    {
        $timezone = 'Asia/Jakarta';

        $start = Carbon::parse($schedule->start_time, $timezone);
        $end = Carbon::parse($schedule->end_time, $timezone);

        return [
            'summary' => '[Workspace Schedule] ' . $schedule->title,
            'description' => $schedule->description ?? 'Jadwal kolaborasi tim di Workspace.',
            'start' => [
                'dateTime' => $start->toRfc3339String(),
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $end->toRfc3339String(),
                'timeZone' => $timezone,
            ],
        ];
    }

    private function eventPayload(PersonalSchedule $schedule): array
    {
        $timezone = $schedule->timezone ?: 'Asia/Jakarta';

        return [
            'summary' => $schedule->title,
            'description' => $schedule->description,
            'location' => $schedule->location,
            'start' => [
                'dateTime' => $schedule->startDateTime()->timezone($timezone)->toRfc3339String(),
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $schedule->endDateTime()->timezone($timezone)->toRfc3339String(),
                'timeZone' => $timezone,
            ],
        ];
    }

    private function validAccessToken(User $user): string
    {
        $expiresAt = $user->google_token_expires_at
            ? Carbon::parse($user->google_token_expires_at)
            : null;

        if ($user->google_access_token && $expiresAt && $expiresAt->isFuture()) {
            return $user->google_access_token;
        }

        if (! $user->google_refresh_token) {
            return $user->google_access_token ?? '';
        }

        $response = Http::asForm()
            ->withOptions(['verify' => config('services.google.guzzle.verify')])
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $user->google_refresh_token,
                'grant_type' => 'refresh_token',
            ])
            ->throw()
            ->json();

        $user->forceFill([
            'google_access_token' => $response['access_token'] ?? $user->google_access_token,
            'google_token_expires_at' => now()->addSeconds($response['expires_in'] ?? 3600),
        ])->save();

        return $user->google_access_token;
    }

    private function redirectUri(): string
    {
        return config('services.google.calendar_redirect') ?: route('google.calendar.callback');
    }
}