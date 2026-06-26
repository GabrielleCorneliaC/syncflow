<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PersonalTask;
use App\Models\CollaborativeTask;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ─────────────────────────────────────────────────────────────
        // 1. STATISTIK PROGRESS (Poin 1, 2, 3)
        // ─────────────────────────────────────────────────────────────
        // Ambil total task & task yang done
        $tasksDone = PersonalTask::where('user_id', $user->id)->where('status', 'done')->count();
        $tasksTotal = PersonalTask::where('user_id', $user->id)->count();
        
        // Progress Bar = Rata-rata dari kolom 'progress' di semua personal task
        $progressAvg = PersonalTask::where('user_id', $user->id)->avg('progress');
        $progressPct = $progressAvg ? round($progressAvg) : 0;

        $stats = [
            'tasks_done'     => $tasksDone,
            'tasks_total'    => $tasksTotal,
            'progress_pct'   => $progressPct,
        ];

        // ─────────────────────────────────────────────────────────────
        // 2. NEXT 7 DAYS (Poin 4: Hilangkan yang sudah Done)
        // ─────────────────────────────────────────────────────────────
        $upcomingItems = PersonalTask::where('user_id', $user->id)
            ->where('status', '!=', 'done') // Jangan tampilkan yang done
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [\Carbon\Carbon::now(), \Carbon\Carbon::now()->addDays(7)])
            ->orderBy('due_date', 'asc')
            ->take(4)
            ->get()
            ->map(function ($task) {
                return (object) [
                    'title' => $task->title,
                    'date'  => $task->due_date
                ];
            });

        // ─────────────────────────────────────────────────────────────
        // 3. TUGAS PERSONAL BELUM SELESAI (Poin 6: Hilangkan yang Done)
        // ─────────────────────────────────────────────────────────────
        $pendingPersonalTasks = PersonalTask::where('user_id', $user->id)
            ->where('status', '!=', 'done') // Jangan tampilkan yang done
            ->orderBy('due_date', 'asc')
            ->take(4) // Tambah jadi 4 biar pas sejajar kalender
            ->get();

        // ─────────────────────────────────────────────────────────────
        // 4. TUGAS KOLABORASI BELUM SELESAI (Hanya untuk user yang login)
        // ─────────────────────────────────────────────────────────────
        // Gunakan relasi collaborativeTasks() milik user agar HANYA tugas 
        // dari workspace yang melibatkan user ini saja yang muncul.
        $pendingCollabTasks = $user->collaborativeTasks()
            ->with(['assignees', 'workspace'])
            ->where('collaborative_tasks.status', '!=', 'done') // Jangan tampilkan yang done
            ->orderBy('collaborative_tasks.created_at', 'desc') // Urutkan dari yang terbaru
            ->take(4) // Tambah jadi 4 biar pas sejajar kalender
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'upcomingItems',
            'pendingPersonalTasks',
            'pendingCollabTasks'
        ));
    }
}

