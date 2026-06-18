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
            'comment' => 'nullable|string|required_without:attachment',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('task-comments', 'public');
        }

        $comment = TaskComment::create([
            'collaborative_task_id' => $data['collaborative_task_id'],
            'user_id' => auth()->id(),
            'comment' => $data['comment'] ?? '',
            'attachment_path' => $attachmentPath
        ]);

        $task = $comment->task;

        if ($task && $task->assignee_id && $task->assignee_id !== auth()->id()) {
            $assignee = \App\Models\User::find($task->assignee_id);

            if ($assignee && $assignee->email) {
                $taskTitle = e($task->title ?? $task->name ?? 'Tugas');
                $commentText = e($comment->comment ?: 'Mengirim lampiran pada tugas ini.');
                $senderName = e(auth()->user()->name ?? 'Anggota tim');
                $assigneeName = e($assignee->name ?? 'Anggota tim');
                $createdAt = $comment->created_at->format('d M Y H:i');

                $emailBody = "
                <div style='font-family: Arial, sans-serif; background:#f7f5f0; padding:24px;'>
                    <div style='max-width:600px; margin:0 auto; background:white; border-radius:16px; overflow:hidden; border:1px solid #eee;'>
                        <div style='background:#f4ecdf; padding:20px 24px;'>
                            <h2 style='margin:0; color:#222;'>Komentar Baru di SyncFlow</h2>
                            <p style='margin:6px 0 0; color:#666; font-size:14px;'>
                                Ada komentar baru pada tugas yang menjadi tanggung jawab kamu.
                            </p>
                        </div>

                        <div style='padding:24px; color:#333;'>
                            <p style='margin-top:0;'>Halo <strong>{$assigneeName}</strong>,</p>

                            <p><strong>{$senderName}</strong> menambahkan komentar baru pada tugas:</p>

                            <div style='background:#fff7fb; border-left:4px solid #c8216b; padding:14px 16px; border-radius:10px; margin:16px 0;'>
                                <p style='margin:0; font-size:13px; color:#777;'>Tugas</p>
                                <p style='margin:4px 0 0; font-size:16px; font-weight:bold; color:#222;'>{$taskTitle}</p>
                            </div>

                            <div style='background:#f8f8f8; padding:14px 16px; border-radius:10px; margin:16px 0;'>
                                <p style='margin:0; font-size:13px; color:#777;'>Komentar</p>
                                <p style='margin:6px 0 0; font-size:15px; color:#333; line-height:1.5;'>
                                    “{$commentText}”
                                </p>
                            </div>

                            <p style='font-size:13px; color:#777;'>Waktu: {$createdAt}</p>

                            <p style='margin-top:20px;'>
                                Silakan buka SyncFlow untuk melihat diskusi lengkap dan memberikan tanggapan.
                            </p>

                            <p style='margin-bottom:0; color:#555;'>
                                Terima kasih,<br>
                                <strong>Tim SyncFlow</strong>
                            </p>
                        </div>
                    </div>
                </div>
                ";

                Mail::html($emailBody, function ($message) use ($assignee) {
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
            'comment' => 'nullable|string|required_without:attachment',
            'attachment' => 'nullable|file|max:10240'
        ]);

        if ($request->hasFile('attachment')) {
            if ($taskComment->attachment_path) {
                Storage::disk('public')->delete($taskComment->attachment_path);
            }

            $taskComment->attachment_path = $request->file('attachment')->store('task-comments', 'public');
        }

        $taskComment->comment = $data['comment'] ?? '';
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
