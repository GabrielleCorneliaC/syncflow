<?php

namespace App\Services;

use App\Models\CollaborativeTask;
use App\Models\PersonalSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    public function authorizationUrl(): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
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
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => $this->redirectUri(),
            'grant_type' => 'authorization_code',
        ])->throw()->json();

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
                ->put("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$schedule->google_calendar_event_id}", $payload)
                ->throw()
                ->json();
        } else {
            $response = Http::withToken($token)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $payload)
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
            ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$schedule->google_calendar_event_id}")
            ->throw();
    }

    public function syncCollaborativeTask(CollaborativeTask $task, User $user): ?array
    {
        if (! $task->deadline || ! $user->hasGoogleCalendarConnected()) {
            return null;
        }

        $payload = $this->collaborativeTaskPayload($task);
        $existingEventId = $task->assignees
            ->firstWhere('id', $user->id)
            ?->pivot
            ?->google_calendar_event_id;

        if ($existingEventId) {
            return Http::withToken($this->validAccessToken($user))
                ->put("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$existingEventId}", $payload)
                ->throw()
                ->json();
        }

        return Http::withToken($this->validAccessToken($user))
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $payload)
            ->throw()
            ->json();
    }

    public function deleteCollaborativeTask(CollaborativeTask $task, User $user): void
    {
        $eventId = $task->assignees
            ->firstWhere('id', $user->id)
            ?->pivot
            ?->google_calendar_event_id;

        if (! $eventId || ! $user->hasGoogleCalendarConnected()) {
            return;
        }

        Http::withToken($this->validAccessToken($user))
            ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}")
            ->throw();
    }

    private function validAccessToken(User $user): string
    {
        if ($user->google_access_token && optional($user->google_token_expires_at)->isFuture()) {
            return $user->google_access_token;
        }

        if (! $user->google_refresh_token) {
            return $user->google_access_token;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $user->google_refresh_token,
            'grant_type' => 'refresh_token',
        ])->throw()->json();

        $user->forceFill([
            'google_access_token' => $response['access_token'] ?? $user->google_access_token,
            'google_token_expires_at' => now()->addSeconds($response['expires_in'] ?? 3600),
        ])->save();

        return $user->google_access_token;
    }

    private function eventPayload(PersonalSchedule $schedule): array
    {
        $timezone = config('app.timezone', 'UTC');

        return [
            'summary' => $schedule->title,
            'description' => $schedule->description,
            'location' => $schedule->location,
            'start' => [
                'dateTime' => $schedule->startDateTime()->toRfc3339String(),
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $schedule->endDateTime()->toRfc3339String(),
                'timeZone' => $timezone,
            ],
        ];
    }

    private function collaborativeTaskPayload(CollaborativeTask $task): array
    {
        $description = trim(($task->description ? $task->description."\n\n" : '')."Status: {$task->status}\nProgress: {$task->progress}%");

        return [
            'summary' => '[SyncFlow Task] '.$task->name,
            'description' => $description,
            'start' => [
                'date' => $task->deadline->format('Y-m-d'),
            ],
            'end' => [
                'date' => $task->calendarEndDate()->format('Y-m-d'),
            ],
            'reminders' => [
                'useDefault' => true,
            ],
        ];
    }

    private function redirectUri(): string
    {
        return config('services.google.redirect_uri') ?: route('google.calendar.callback');
    }
}
