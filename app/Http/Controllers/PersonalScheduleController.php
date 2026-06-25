<?php

namespace App\Http\Controllers;

use App\Models\PersonalSchedule;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Throwable;

class PersonalScheduleController extends Controller
{
    public function store(Request $request, GoogleCalendarService $googleCalendar)
    {
        $schedule = PersonalSchedule::create($this->validatedData($request) + [
            'user_id' => auth()->id(),
        ]);

        $this->syncGoogleCalendar($schedule, $googleCalendar);

        return redirect()->route('personal.index')->with('success', 'Schedule berhasil dibuat.');
    }

    public function update(Request $request, PersonalSchedule $schedule, GoogleCalendarService $googleCalendar)
    {
        $this->authorizeSchedule($schedule);

        $schedule->update($this->validatedData($request));
        $this->syncGoogleCalendar($schedule, $googleCalendar);

        return redirect()->route('personal.index')->with('success', 'Schedule berhasil diperbarui.');
    }

    public function destroy(PersonalSchedule $schedule, GoogleCalendarService $googleCalendar)
    {
        $this->authorizeSchedule($schedule);

        try {
            $googleCalendar->deleteSchedule($schedule);
        } catch (Throwable $exception) {
            report($exception);
        }

        $schedule->delete();

        return redirect()->route('personal.index')->with('success', 'Schedule berhasil dihapus.');
    }

    public function events()
    {
        $events = PersonalSchedule::where('user_id', auth()->id())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(fn (PersonalSchedule $schedule) => [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->startDateTime()->toIso8601String(),
                'end' => $schedule->endDateTime()->toIso8601String(),
                'extendedProps' => [
                    'description' => $schedule->description,
                    'location' => $schedule->location,
                ],
            ]);

        return response()->json($events);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:time'],
            'timezone' => ['required', 'in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeSchedule(PersonalSchedule $schedule): void
    {
        abort_unless($schedule->user_id === auth()->id(), 403);
    }

    private function syncGoogleCalendar(PersonalSchedule $schedule, GoogleCalendarService $googleCalendar): void
    {
        try {
            $googleCalendar->syncSchedule($schedule);
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('warning', 'Schedule tersimpan, tetapi sinkronisasi Google Calendar gagal.');
        }
    }
}
