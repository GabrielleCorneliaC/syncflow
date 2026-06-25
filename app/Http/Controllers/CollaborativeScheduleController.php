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
        // 1. Validasi input yang masuk (Harus sesuai dengan name di form Modal Add Schedule)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
        ]);

        // 2. Bersihkan format jam bawaan HTML5 (agar aman dibaca database)
        $startFormat = Carbon::parse(str_replace('T', ' ', $validated['start_time']))->toDateTimeString();
        $endFormat = Carbon::parse(str_replace('T', ' ', $validated['end_time']))->toDateTimeString();

        // 3. Simpan jadwal ke database
        $schedule = $workspace->collaborativeSchedules()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $startFormat,
            'end_time' => $endFormat,
        ]);

        // 4. Sinkronisasi ke Google Calendar milik pembuat
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
        // 1. Validasi input (sama seperti store)
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time'  => ['required', 'string'],
            'end_time'    => ['required', 'string'],
        ]);

        // 2. Cari jadwal milik workspace ini
        $schedule = CollaborativeSchedule::where('workspace_id', $workspace->id)->findOrFail($schedule_id);

        // 3. Parse format datetime dari HTML5 datetime-local
        $startFormat = Carbon::parse(str_replace('T', ' ', $validated['start_time']))->toDateTimeString();
        $endFormat   = Carbon::parse(str_replace('T', ' ', $validated['end_time']))->toDateTimeString();

        // 4. Update data di database
        $schedule->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time'  => $startFormat,
            'end_time'    => $endFormat,
        ]);

        // 5. Sinkronisasi ulang ke Google Calendar
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

        // Hapus jadwal dari database
        $schedule->delete();

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}