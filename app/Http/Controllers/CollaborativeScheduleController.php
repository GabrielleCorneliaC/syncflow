<?php

namespace App\Http\Controllers;

use App\Models\CollaborativeTask;
use App\Models\User;
use App\Models\Workspace;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CollaborativeTaskController extends Controller
{
    public function show(Workspace $workspace, CollaborativeTask $task)
    {
        abort_unless($task->workspace_id === $workspace->id, 404);

        return back();
    }

    public function store(Request $request, Workspace $workspace, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:todo,pending,review,done,overdue'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // Kumpulkan data assignee dari input array maupun single input
        $assigneeIds = collect($validated['assignee_ids'] ?? [])
            ->when(isset($validated['assignee_id']), fn ($ids) => $ids->push($validated['assignee_id']))
            ->filter()
            ->unique()
            ->values();

        // Ambil ID dari tabel workspace_members untuk validasi kepesertaan kelompok
        $workspaceMemberIds = DB::table('workspace_members')
            ->where('workspace_id', $workspace->id)
            ->pluck('user_id');

        // Saring assignee: Pastikan user yang dipilih memang terdaftar di workspace tersebut
        if ($workspaceMemberIds->isNotEmpty()) {
            $assigneeIds = $assigneeIds->intersect($workspaceMemberIds)->values();
        }

        // Jika setelah disaring ternyata kosong, paksa masukkan user yang sedang login agar tidak error required kosong
        if ($assigneeIds->isEmpty() && auth()->check()) {
            $assigneeIds->push(auth()->id());
        }

        $task = DB::transaction(function () use ($workspace, $validated, $assigneeIds) {
            $task = $workspace->collaborativeTasks()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'status' => $validated['status'],
                'progress' => $validated['progress'],
            ]);

            $task->assignees()->sync($assigneeIds->all());

            return $task->load('assignees');
        });

        $this->syncAssigneeCalendars($task, $googleCalendar);

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Task berhasil dibuat dan dikirim ke Google Calendar assignee yang sudah connect.');
    }

    public function updateStatus(Request $request, CollaborativeTask $task, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:todo,pending,review,done,overdue'],
        ]);

        $task->update([
            'status' => $validated['status'],
            'progress' => $validated['status'] === 'done' ? 100 : min($task->progress, 99),
        ]);

        $this->syncAssigneeCalendars($task->load('assignees'), $googleCalendar);

        return response()->json(['message' => 'Status task berhasil diperbarui.']);
    }

    public function destroy(Workspace $workspace, CollaborativeTask $task, GoogleCalendarService $googleCalendar)
    {
        abort_unless($task->workspace_id === $workspace->id, 404);

        foreach ($task->assignees as $assignee) {
            $this->deleteAssigneeCalendarEvent($task, $assignee, $googleCalendar);
        }

        $task->delete();

        return redirect()->route('workspaces.show', ['workspace' => $workspace->id]);
    }

    private function syncAssigneeCalendars(CollaborativeTask $task, GoogleCalendarService $googleCalendar): void
    {
        if (! $task->deadline) {
            return;
        }

        foreach ($task->assignees as $assignee) {
            // Sengaja dilepas try-catch-nya sementara agar jika query updatePivot kamu crash / error (kolom tidak ketemu), langsung memicu halaman merah.
            $event = $googleCalendar->syncCollaborativeTask($task, $assignee);

            if ($event && isset($event['id'])) {
                $task->assignees()->updateExistingPivot($assignee->id, [
                    'google_calendar_event_id' => $event['id'],
                    'google_calendar_html_link' => $event['htmlLink'] ?? null,
                ]);
            }
        }
    }

    private function deleteAssigneeCalendarEvent(CollaborativeTask $task, User $assignee, GoogleCalendarService $googleCalendar): void
    {
        try {
            $googleCalendar->deleteCollaborativeTask($task, $assignee);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}