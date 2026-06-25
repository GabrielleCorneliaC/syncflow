<?php

namespace App\Http\Controllers;

use App\Models\CollaborativeTask;
use App\Models\User;
use App\Models\Workspace;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
            // Mengizinkan string datetime standar agar fleksibel dibaca Carbon
            'deadline' => ['nullable', 'string'], 
            'status' => ['required', 'in:todo,pending,review,done,overdue'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $assigneeIds = collect($validated['assignee_ids'] ?? [])
            ->when(isset($validated['assignee_id']), fn ($ids) => $ids->push($validated['assignee_id']))
            ->filter()
            ->unique()
            ->values();

        // Cari ID member + Owner yang sah di workspace ini
        $allowedUserIds = DB::table('workspace_members')
            ->where('workspace_id', $workspace->id)
            ->pluck('user_id')
            ->filter()
            ->unique();

        // Saring assignee
        if ($assigneeIds->isEmpty() && auth()->check()) {
            $assigneeIds->push(auth()->id());
        } else {
            $assigneeIds = $assigneeIds->intersect($allowedUserIds)->values();
            if ($assigneeIds->isEmpty() && auth()->check()) {
                $assigneeIds->push(auth()->id());
            }
        }

        $task = DB::transaction(function () use ($workspace, $validated, $assigneeIds, $request) {
            // 💡 SOLUSI JAM TERPOTONG: Bersihkan format 'T' HTML5 datetime-local secara paksa ke standar format DateTime
            $deadlineFormat = null;
            if ($request->filled('deadline')) {
                $cleanDeadline = str_replace('T', ' ', $validated['deadline']);
                $deadlineFormat = Carbon::parse($cleanDeadline)->toDateTimeString();
            }

            $task = $workspace->collaborativeTasks()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'deadline' => $deadlineFormat, // Tersimpan penuh dengan format YYYY-MM-DD HH:MM:SS
                'status' => $validated['status'],
                'progress' => $validated['progress'],
            ]);

            $task->assignees()->sync($assigneeIds->all());

            return $task->load('assignees');
        });

        // Paksa sinkronisasi ulang ke Google Calendar
        $this->syncAssigneeCalendars($task, $googleCalendar);

        return redirect()
            ->route('workspaces.show', ['workspace' => $workspace->id])
            ->with('success', 'Task berhasil dibuat dan dikirim ke Google Calendar.');
    }

    public function updateStatus(Request $request, CollaborativeTask $task, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'status' => ['required'],
        ]);

        $statusInput = $validated['status'];
        
        if ($statusInput === true || $statusInput === 'true') {
            $newStatus = 'done';
        } else {
            $newStatus = 'todo';
        }

        $task->update([
            'status' => $newStatus,
        ]);

        try {
            $this->syncAssigneeCalendars($task->load('assignees'), $googleCalendar);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'progress' => $task->progress
        ]);
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
        if (!$task->deadline) {
            return;
        }

        foreach ($task->assignees as $assignee) {
            try {
                // Pastikan model user memuat relasi/token terbarunya
                $user = User::find($assignee->id);
                if ($user) {
                    $event = $googleCalendar->syncCollaborativeTask($task, $user);

                    if ($event) {
                        $task->assignees()->updateExistingPivot($user->id, [
                            'google_calendar_event_id' => $event['id'] ?? null,
                            'google_calendar_html_link' => $event['htmlLink'] ?? null,
                        ]);
                    }
                }
            } catch (Throwable $exception) {
                report($exception);
                session()->flash('warning', 'Task tersimpan, tetapi Google Calendar gagal disinkronkan.');
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