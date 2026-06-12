<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class TaskCommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'collaborative_task_id' => 'required|exists:collaborative_tasks,id',
            'comment' => 'required|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('task-comments', 'public');
        }

        $comment = TaskComment::create([
            'collaborative_task_id' => $data['collaborative_task_id'],
            'user_id' => auth()->id(),
            'comment' => $data['comment'],
            'attachment_path' => $attachmentPath
        ]);

        $task = $comment->task;

        if ($task && $task->assignee_id && $task->assignee_id !== auth()->id()) {
            $assignee = \App\Models\User::find($task->assignee_id);

            if ($assignee && $assignee->email) {
                Mail::raw('Ada komentar baru pada tugas yang menjadi tanggung jawab kamu di SyncFlow.', function ($message) use ($assignee) {
                    $message->to($assignee->email);
                    $message->subject('Komentar Baru di SyncFlow');
                });
            }
        }

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }

    public function update(Request $request, TaskComment $taskComment)
    {
        if ($taskComment->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'comment' => 'required|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        if ($request->hasFile('attachment')) {
            if ($taskComment->attachment_path) {
                Storage::disk('public')->delete($taskComment->attachment_path);
            }

            $taskComment->attachment_path = $request->file('attachment')->store('task-comments', 'public');
        }

        $taskComment->comment = $data['comment'];
        $taskComment->save();

        return response()->json([
            'success' => true,
            'comment' => $taskComment->load('user')
        ]);
    }

    public function destroy(TaskComment $taskComment)
    {
        if ($taskComment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($taskComment->attachment_path) {
            Storage::disk('public')->delete($taskComment->attachment_path);
        }

        $taskComment->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
