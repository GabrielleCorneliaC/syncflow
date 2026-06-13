<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Controllers\CollaborativeTaskController;
use App\Http\Controllers\CollaborativeScheduleController;
use App\Http\Controllers\ResourceLinkController;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\TaskCommentController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::patch('/info', [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('update.password');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update.avatar');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/users')->name('admin.users.')->middleware('can:admin')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::delete('/{user}', [ProfileController::class, 'adminDestroy'])->name('destroy');
    });

    // Workspaces - Gabrielle
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::get('/api/unsplash/search', [WorkspaceController::class, 'unsplashSearch'])->name('api.unsplash.search');

    Route::middleware(['workspace.role'])->group(function () {
        Route::get('/workspaces/{workspace_id}', [WorkspaceController::class, 'show'])->name('workspaces.show');
    });

    Route::middleware(['workspace.role:admin'])->group(function () {
        Route::delete('/workspaces/{workspace_id}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');
        Route::patch('/workspaces/{workspace_id}', [WorkspaceController::class, 'update'])->name('workspaces.update');
        Route::post('/workspaces/{workspace_id}/members', [WorkspaceController::class, 'invite'])->name('workspaces.members.store');
    });

    // Task Comments - Felita
    Route::post('/task-comments', [TaskCommentController::class, 'store'])->name('task-comments.store');
    Route::put('/task-comments/{taskComment}', [TaskCommentController::class, 'update'])->name('task-comments.update');
    Route::delete('/task-comments/{taskComment}', [TaskCommentController::class, 'destroy'])->name('task-comments.destroy');

    // Collaborative features - masih di-comment sampai modul task/schedule/resource siap
    Route::post('/workspaces/{workspace_id}/tasks', [CollaborativeTaskController::class, 'store'])->name('workspaces.tasks.store');
    Route::get('/workspaces/{workspace_id}/tasks/{task_id}', [CollaborativeTaskController::class, 'show'])->name('workspaces.tasks.show');
    Route::patch('/workspaces/tasks/{task_id}/status', [CollaborativeTaskController::class, 'updateStatusAjax'])->name('workspaces.tasks.updateStatus');
    Route::delete('/workspaces/{workspace_id}/tasks/{task_id}', [CollaborativeTaskController::class, 'destroy'])->name('workspaces.tasks.destroy');

    Route::post('/workspaces/{workspace_id}/schedules', [CollaborativeScheduleController::class, 'store'])->name('workspaces.schedules.store');
    Route::delete('/workspaces/{workspace_id}/schedules/{schedule_id}', [CollaborativeScheduleController::class, 'destroy'])->name('workspaces.schedules.destroy');

    Route::post('/workspaces/{workspace_id}/resources', [ResourceLinkController::class, 'store'])->name('workspaces.resources.store');
    Route::delete('/workspaces/{workspace_id}/resources/{resource_id}', [ResourceLinkController::class, 'destroy'])->name('workspaces.resources.destroy');

    Route::post('/workspaces/{workspace_id}/members', [WorkspaceController::class, 'invite'])->name('workspaces.invite');
    Schedule::command('tasks:check-overdue')->hourly();
});
