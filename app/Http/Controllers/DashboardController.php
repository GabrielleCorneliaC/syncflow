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

        $tasksDone = PersonalTask::where('user_id', $user->id)->where('status', 'done')->count();
        $tasksTotal = PersonalTask::where('user_id', $user->id)->count();
        
        $progressAvg = PersonalTask::where('user_id', $user->id)->avg('progress');
        $progressPct = $progressAvg ? round($progressAvg) : 0;

        $stats = [
            'tasks_done'     => $tasksDone,
            'tasks_total'    => $tasksTotal,
            'progress_pct'   => $progressPct,
        ];

        $upcomingItems = PersonalTask::where('user_id', $user->id)
            ->where('status', '!=', 'done') 
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

        $pendingPersonalTasks = PersonalTask::where('user_id', $user->id)
            ->where('status', '!=', 'done') 
            ->orderBy('due_date', 'asc')
            ->take(4) 
            ->get();


        $pendingCollabTasks = $user->collaborativeTasks()
            ->with(['assignees', 'workspace'])
            ->where('collaborative_tasks.status', '!=', 'done') 
            ->orderBy('collaborative_tasks.created_at', 'desc') 
            ->take(4) 
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'upcomingItems',
            'pendingPersonalTasks',
            'pendingCollabTasks'
        ));
    }
}

