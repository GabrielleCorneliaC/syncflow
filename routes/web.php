<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\PersonalScheduleController;
use App\Http\Controllers\PersonalTaskController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Controllers\CollaborativeTaskController;
use App\Http\Controllers\CollaborativeScheduleController;
use App\Http\Controllers\ResourceLinkController;
use App\Http\Controllers\TaskCommentController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| Hanya dapat diakses ketika user belum login.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Route untuk menampilkan halaman forgot password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');

// Route untuk langsung mereset password tanpa email
Route::post('/forgot-password', [AuthController::class, 'resetPasswordDirect'])->name('password.update.direct');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Semua route di bawah wajib login.
*/
Route::middleware('auth')->group(function () {

    // Dev bypass login: akses langsung sebagai user ID 1
    // Route::get('/dev/login-as-user-1', function () {
    //     Auth::loginUsingId(1);
    //     request()->session()->regenerate();
    //     return redirect()->route('personal.index');
    // })->name('dev.login-as-user-1');

    // Home dan dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Personal task dan personal schedule
    Route::get('/personal', [PersonalTaskController::class, 'index'])->name('personal.index');

    Route::post('/personal/tasks', [PersonalTaskController::class, 'store'])->name('personal.tasks.store');
    Route::put('/personal/tasks/{task}', [PersonalTaskController::class, 'update'])->name('personal.tasks.update');
    Route::delete('/personal/tasks/{task}', [PersonalTaskController::class, 'destroy'])->name('personal.tasks.destroy');
    Route::patch('/personal/tasks/{task}/status', [PersonalTaskController::class, 'updateStatus'])->name('personal.tasks.status');

    Route::post('/personal/schedules', [PersonalScheduleController::class, 'store'])->name('personal.schedules.store');
    Route::put('/personal/schedules/{schedule}', [PersonalScheduleController::class, 'update'])->name('personal.schedules.update');
    Route::delete('/personal/schedules/{schedule}', [PersonalScheduleController::class, 'destroy'])->name('personal.schedules.destroy');
    Route::get('/personal/schedules/events', [PersonalScheduleController::class, 'events'])->name('personal.schedules.events');

    // Google Calendar
    Route::get('/google-calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::patch('/info', [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('update.password');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update.avatar');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Admin user management
    Route::prefix('admin/users')
        ->name('admin.users.')
        ->middleware('can:admin')
        ->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::delete('/{user}', [ProfileController::class, 'adminDestroy'])->name('destroy');
        });

    // Workspace umum
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::get('/api/unsplash/search', [WorkspaceController::class, 'unsplashSearch'])->name('api.unsplash.search');

    // Workspace yang hanya dapat diakses member workspace
    Route::middleware('workspace.role')->group(function () {
        Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->name('workspaces.show');

        // Collaborative task
        Route::post('/workspaces/{workspace}/tasks', [CollaborativeTaskController::class, 'store'])
            ->name('workspaces.tasks.store');

        Route::get('/workspaces/{workspace}/tasks/{task}', [CollaborativeTaskController::class, 'show'])
            ->name('workspaces.tasks.show');

        Route::put('/workspaces/{workspace}/tasks/{task}', [CollaborativeTaskController::class, 'update'])
            ->name('workspaces.tasks.update');

        Route::delete('/workspaces/{workspace}/tasks/{task}', [CollaborativeTaskController::class, 'destroy'])
            ->name('workspaces.tasks.destroy');

        // Collaborative schedule
        Route::post('/workspaces/{workspace}/schedules', [CollaborativeScheduleController::class, 'store'])
            ->name('workspaces.schedules.store');
        
        Route::put('/workspaces/{workspace}/schedules/{schedule}', [CollaborativeScheduleController::class, 'update'])
            ->name('workspaces.schedules.update');

        Route::delete('/workspaces/{workspace}/schedules/{schedule}', [CollaborativeScheduleController::class, 'destroy'])
            ->name('workspaces.schedules.destroy');

        // Resource link
        Route::post('/workspaces/{workspace}/resources', [ResourceLinkController::class, 'store'])
            ->name('workspaces.resources.store');

        Route::put('/workspaces/{workspace}/resources/{resource}', [ResourceLinkController::class, 'update'])
            ->name('workspaces.resources.update');

        Route::delete('/workspaces/{workspace}/resources/{resource}', [ResourceLinkController::class, 'destroy'])
            ->name('workspaces.resources.destroy');

        // Task comment
        Route::post('/task-comments', [TaskCommentController::class, 'store'])->name('task-comments.store');
        Route::put('/task-comments/{taskComment}', [TaskCommentController::class, 'update'])->name('task-comments.update');
        Route::delete('/task-comments/{taskComment}', [TaskCommentController::class, 'destroy'])->name('task-comments.destroy');
    });

    Route::put('/workspaces/tasks/{task}/status', [CollaborativeTaskController::class, 'updateStatus'])
        ->name('workspaces.tasks.updateStatus');
    
    Route::patch('/workspaces/tasks/{task}/status', [CollaborativeTaskController::class, 'updateStatus'])
        ->name('workspaces.tasks.updateStatusPatch');

    // Khusus admin workspace
    Route::middleware('workspace.role:admin')->group(function () {
        Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');
        Route::patch('/workspaces/{workspace}', [WorkspaceController::class, 'update'])->name('workspaces.update');

        Route::post('/workspaces/{workspace}/members', [WorkspaceController::class, 'invite'])
            ->name('workspaces.members.store');
    });
});