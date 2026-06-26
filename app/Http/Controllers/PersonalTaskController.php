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
        'status' => ['required', 'in:todo,in_progress,done'],
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

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => ['required', 'in:todo,in_progress,done'],
        'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
    ]);

    $task = PersonalTask::findOrFail($id);

    $progress = match ($request->status) {
        'todo' => 0,
        'done' => 100,
        'in_progress' => $request->progress,
    };

    $task->update([
        'status' => $request->status,
        'progress' => $progress,
    ]);

    return response()->json([
        'success' => true,
    ]);
}


    private function authorizeTask(PersonalTask $task): void
    {
        abort_unless($task->user_id === auth()->id(), 403);
    }
}
