<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskComment;

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
            $attachmentPath = $request
                ->file('attachment')
                ->store('task-comments', 'public');
        }

        $comment = TaskComment::create([
            'collaborative_task_id' => $data['collaborative_task_id'],
            'user_id' => auth()->id(),
            'comment' => $data['comment'],
            'attachment_path' => $attachmentPath
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }
}
