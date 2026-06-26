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

        $task->load('comments.user'); 

        return view('tasks.show', compact('workspace', 'task'));
    }

    
    public function store(Request $request, Workspace $workspace, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'string'], 
            'status' => ['required', 'in:todo,pending,review,done,overdue'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $assigneeIds = collect($validated['assignee_ids'] ?? [])
            ->when(isset($validated['assignee_id']), fn ($ids) => $ids->push($validated['assignee_id']))
            ->filter()->unique()->values();

        $allowedUserIds = DB::table('workspace_members')->where('workspace_id', $workspace->id)->pluck('user_id')->filter()->unique();

        if ($assigneeIds->isEmpty() && auth()->check()) {
            $assigneeIds->push(auth()->id());
        } else {
            $assigneeIds = $assigneeIds->intersect($allowedUserIds)->values();
            if ($assigneeIds->isEmpty() && auth()->check()) $assigneeIds->push(auth()->id());
        }

        $task = DB::transaction(function () use ($workspace, $validated, $assigneeIds, $request) {
            $deadlineFormat = null;
            if ($request->filled('deadline')) {
                $deadlineFormat = Carbon::parse(str_replace('T', ' ', $validated['deadline']))->toDateTimeString();
            }

            $task = $workspace->collaborativeTasks()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'deadline' => $deadlineFormat,
                'status' => $validated['status'],
                'progress' => $validated['progress'],
            ]);

            $task->assignees()->sync($assigneeIds->all());
            return $task->load('assignees');
        });

        $this->syncAssigneeCalendars($task, $googleCalendar);

        return redirect()->route('workspaces.show', ['workspace' => $workspace->id])
                         ->with('success', 'Task berhasil dibuat dan dikirim ke Google Calendar.');
    }

    public function update(Request $request, Workspace $workspace, CollaborativeTask $task, GoogleCalendarService $googleCalendar)
    {
        abort_unless($task->workspace_id === $workspace->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'string'], 
            'status' => ['required', 'in:todo,pending,review,done,overdue'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $assigneeIds = collect($validated['assignee_ids'] ?? [])
            ->when(isset($validated['assignee_id']), fn ($ids) => $ids->push($validated['assignee_id']))
            ->filter()->unique()->values();

        $allowedUserIds = DB::table('workspace_members')->where('workspace_id', $workspace->id)->pluck('user_id')->filter()->unique();

        if ($assigneeIds->isEmpty() && auth()->check()) {
            $assigneeIds->push(auth()->id());
        } else {
            $assigneeIds = $assigneeIds->intersect($allowedUserIds)->values();
            if ($assigneeIds->isEmpty() && auth()->check()) $assigneeIds->push(auth()->id());
        }

        $oldAssignees = $task->assignees;
        $newAssigneeIdsArray = $assigneeIds->all();
        $removedAssignees = $oldAssignees->filter(function ($user) use ($newAssigneeIdsArray) {
            return !in_array($user->id, $newAssigneeIdsArray);
        });

        foreach ($removedAssignees as $removedUser) {
            $this->deleteAssigneeCalendarEvent($task, $removedUser, $googleCalendar);
        }

        DB::transaction(function () use ($task, $validated, $newAssigneeIdsArray, $request) {
            $deadlineFormat = null;
            if ($request->filled('deadline')) {
                $deadlineFormat = Carbon::parse(str_replace('T', ' ', $validated['deadline']))->toDateTimeString();
            }

            $task->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'deadline' => $deadlineFormat,
                'status' => $validated['status'],
                'progress' => $validated['progress'],
            ]);

            $task->assignees()->sync($newAssigneeIdsArray);
        });

        $this->syncAssigneeCalendars($task->fresh('assignees'), $googleCalendar);

        return redirect()->route('workspaces.show', ['workspace' => $workspace->id])->with('success', 'Tugas berhasil diperbarui.');
    }

    public function updateStatus(Request $request, CollaborativeTask $task, GoogleCalendarService $googleCalendar)
    {
        $validated = $request->validate(['status' => ['required']]);
        $newStatus = ($validated['status'] === true || $validated['status'] === 'true') ? 'done' : 'todo';

        $task->update(['status' => $newStatus]);

        try {
            $this->syncAssigneeCalendars($task->fresh('assignees'), $googleCalendar);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json(['success' => true, 'status' => $newStatus, 'progress' => $task->progress]);
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
        if (!$task->deadline) return;

        foreach ($task->assignees as $assignee) {
            try {
                $event = $googleCalendar->syncCollaborativeTask($task, $assignee);

                if ($event && isset($event['id'])) {
                    $task->assignees()->updateExistingPivot($assignee->id, [
                        'google_calendar_event_id' => $event['id'],
                        'google_calendar_html_link' => $event['htmlLink'] ?? null,
                    ]);
                }
            } catch (Throwable $exception) {
                report($exception);
                session()->flash('warning', 'Task tersimpan, tetapi Google Calendar gagal disinkronkan.');
            }
        }
    }

    private function deleteAssigneeCalendarEvent(CollaborativeTask $task, User $assignee, GoogleCalendarService $googleCalendar): void
    {
        if ($assignee->pivot && $assignee->pivot->google_calendar_event_id) {
            try {
                $googleCalendar->deleteCollaborativeTask($task, $assignee);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}