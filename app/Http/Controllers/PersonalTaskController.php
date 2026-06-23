<?php

namespace App\Http\Controllers;

use App\Models\PersonalSchedule;
use App\Models\PersonalTask;
use Illuminate\Http\Request;

class PersonalTaskController extends Controller
{
    public function index()
    {
        $tasks = PersonalTask::where('user_id', auth()->id())
            ->latest('due_date')
            ->latest()
            ->get();

        $schedules = PersonalSchedule::where('user_id', auth()->id())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('personal.index', compact('tasks', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $validated['user_id'] = auth()->id();
        PersonalTask::create($validated);

        return redirect()->route('personal.index')->with('success', 'Task berhasil dibuat.');
    }

    public function update(Request $request, PersonalTask $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $task->update($validated);

        return redirect()->route('personal.index')->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(PersonalTask $task)
    {
        $this->authorizeTask($task);
        $task->delete();

        return redirect()->route('personal.index')->with('success', 'Task berhasil dihapus.');
    }

    public function updateStatus(Request $request, PersonalTask $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'completed' => ['required', 'boolean'],
        ]);

        if ($validated['completed']) {
            $task->update([
                'status' => 'completed',
                'progress_before_completed' => $task->status === 'completed'
                    ? ($task->progress_before_completed ?? 0)
                    : $task->progress,
                'progress' => 100,
            ]);
        } else {
            $task->update([
                'status' => 'pending',
                'progress' => $task->progress_before_completed ?? 0,
                'progress_before_completed' => null,
            ]);
        }

        return response()->json([
            'id' => $task->id,
            'status' => $task->status,
            'progress' => $task->progress,
            'message' => 'Status task berhasil diperbarui.',
        ]);
    }

    private function authorizeTask(PersonalTask $task): void
    {
        abort_unless($task->user_id === auth()->id(), 403);
    }
}
