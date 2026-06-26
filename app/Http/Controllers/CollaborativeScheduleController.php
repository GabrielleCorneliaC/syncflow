<?php

namespace App\Http\Controllers;

use App\Models\CollaborativeSchedule;
use App\Models\Workspace;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;

class CollaborativeScheduleController extends Controller
{
    public function store(Request $request, Workspace $workspace, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
        ]);

        $startFormat = Carbon::parse(str_replace('T', ' ', $validated['start_time']))->toDateTimeString();
        $endFormat = Carbon::parse(str_replace('T', ' ', $validated['end_time']))->toDateTimeString();

        $schedule = $workspace->collaborativeSchedules()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $startFormat,
            'end_time' => $endFormat,
        ]);

        try {
            $googleCalendar->syncCollaborativeSchedule($schedule, auth()->user());
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('warning', 'Jadwal tersimpan, tetapi Google Calendar gagal disinkronkan.');
        }

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Jadwal kolaborasi berhasil ditambahkan.');
    }

    public function update(Request $request, Workspace $workspace, $schedule_id, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time'  => ['required', 'string'],
            'end_time'    => ['required', 'string'],
        ]);

        $schedule = CollaborativeSchedule::where('workspace_id', $workspace->id)->findOrFail($schedule_id);

        $startFormat = Carbon::parse(str_replace('T', ' ', $validated['start_time']))->toDateTimeString();
        $endFormat   = Carbon::parse(str_replace('T', ' ', $validated['end_time']))->toDateTimeString();

        $schedule->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time'  => $startFormat,
            'end_time'    => $endFormat,
        ]);

        try {
            $googleCalendar->syncCollaborativeSchedule($schedule, auth()->user());
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('warning', 'Jadwal diperbarui, tetapi Google Calendar gagal disinkronkan.');
        }

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Workspace $workspace, $schedule_id)
    {
        $schedule = CollaborativeSchedule::where('workspace_id', $workspace->id)->findOrFail($schedule_id);

        $schedule->delete();

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}