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
        // 1. STATISTIK PROGRESS (Kiri Atas)
        // ─────────────────────────────────────────────────────────────
        $tasksDone = PersonalTask::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
           
        $tasksTotal = PersonalTask::where('user_id', $user->id)->count();
       
        // Mencegah error pembagian dengan nol
        $progressPct = $tasksTotal > 0 ? round(($tasksDone / $tasksTotal) * 100) : 0;

        $stats = [
            'tasks_done'     => $tasksDone,
            'tasks_total'    => $tasksTotal,
            'progress_pct'   => $progressPct,
           
            // Info Admin
            'total_users'    => User::count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // ─────────────────────────────────────────────────────────────
        // 2. NEXT 7 DAYS (Kanan Atas)
        // ─────────────────────────────────────────────────────────────
        $upcomingItems = PersonalTask::where('user_id', $user->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->orderBy('due_date', 'asc')
            ->take(4)
            ->get()
            ->map(function ($task) {
                // Di-map menjadi 'date' agar seragam saat dibaca file Blade
                return (object) [
                    'title' => $task->title,
                    'date'  => $task->due_date
                ];
            });

        // ─────────────────────────────────────────────────────────────
        // 3. TUGAS PERSONAL BELUM SELESAI (Kiri Bawah)
        // ─────────────────────────────────────────────────────────────
        $pendingPersonalTasks = PersonalTask::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->take(3)
            ->get();

        // ─────────────────────────────────────────────────────────────
        // 4. TUGAS KOLABORASI BELUM SELESAI (Kanan Bawah)
        // ─────────────────────────────────────────────────────────────
        // Asumsi relasi ke user di CollaborativeTask adalah 'assignees'
        $pendingCollabTasks = CollaborativeTask::with('assignees')
            ->where('status', '!=', 'completed')
            ->latest()
            ->take(3)
            ->get();

        // Pastikan nama file blade-nya 'dashboard.blade.php' (tanpa folder) atau sesuaikan.
        return view('dashboard/index', compact(
            'stats',
            'upcomingItems',
            'pendingPersonalTasks',
            'pendingCollabTasks'
        ));
    }
}

