<?php

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\PersonalScheduleController;
use App\Http\Controllers\PersonalTaskController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Controllers\CollaborativeTaskController;
use App\Http\Controllers\CollaborativeScheduleController;
use App\Http\Controllers\ResourceLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// =========================================================================
// JALAN PINTAS SEMENTARA (Ditaruh di LUAR area auth agar bisa diakses)
// =========================================================================
Route::get('/login', function () {
    return '<h1>Halaman Login Belum Dibuat</h1>
            <p>Klik tombol di bawah untuk masuk paksa ke sistem (Bypass).</p>
            <a href="/auto-login" style="padding:10px; background:#C8216B; color:white; text-decoration:none; border-radius:5px;">Masuk Paksa (Auto-Login)</a>';
})->name('login');

Route::get('/auto-login', function () {
    $user = \App\Models\User::find(1);
    
    if (!$user) {
        return 'Gagal! Kamu harus buka phpMyAdmin dulu dan buat satu data manual di tabel "users" dengan ID = 1.';
    }
    
    \Illuminate\Support\Facades\Auth::login($user);
    return redirect()->intended('/personal');
});
// =========================================================================


// ─────────────────────────────────────────────────────────────────────────────
// SEMUA ROUTE DI BAWAH INI DILINDUNGI auth (harus login)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/personal', [PersonalTaskController::class, 'index'])->name('personal.index');
    Route::post('/personal/tasks', [PersonalTaskController::class, 'store'])->name('personal.tasks.store');
    Route::put('/personal/tasks/{task}', [PersonalTaskController::class, 'update'])->name('personal.tasks.update');
    Route::delete('/personal/tasks/{task}', [PersonalTaskController::class, 'destroy'])->name('personal.tasks.destroy');
    Route::patch('/personal/tasks/{task}/status', [PersonalTaskController::class, 'updateStatus'])->name('personal.tasks.status');

    Route::post('/personal/schedules', [PersonalScheduleController::class, 'store'])->name('personal.schedules.store');
    Route::put('/personal/schedules/{schedule}', [PersonalScheduleController::class, 'update'])->name('personal.schedules.update');
    Route::delete('/personal/schedules/{schedule}', [PersonalScheduleController::class, 'destroy'])->name('personal.schedules.destroy');
    Route::get('/personal/schedules/events', [PersonalScheduleController::class, 'events'])->name('personal.schedules.events');

    Route::get('/google-calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');

    // ── Daftar semua workspace milik / yang diikuti user ─────────────────────
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');

    // ── Buat workspace baru ───────────────────────────────────────────────────
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');

    // ── Proxy pencarian foto Unsplash ─────────────────────────────────────────
    Route::get('/api/unsplash/search', [WorkspaceController::class, 'unsplashSearch'])->name('api.unsplash.search');


    // ─────────────────────────────────────────────────────────────────────────
    // GROUP: TUGAS GABRIELLE (Detail, Delete, Invite Workspace)
    // ─────────────────────────────────────────────────────────────────────────
    
    // 1. Rute untuk Admin & Collaborator (Hanya bisa melihat isi folder)
    Route::middleware(['workspace.role'])->group(function () {
        Route::get('/workspaces/{workspace_id}', [WorkspaceController::class, 'show'])->name('workspaces.show');
    });

    // 2. Rute khusus Admin/Owner (Hapus & Undang Anggota)
    Route::middleware(['workspace.role:admin'])->group(function () {
        Route::delete('/workspaces/{workspace_id}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');
        Route::post('/workspaces/{workspace_id}/members', [WorkspaceController::class, 'invite'])->name('workspaces.members.store');
    });


    // ─────────────────────────────────────────────────────────────────────────
    // GROUP: TUGAS KEVIN DLL (Tetap di-comment agar tidak error)
    // ─────────────────────────────────────────────────────────────────────────
    // Route::post('/workspaces/{workspace}/schedules', [CollaborativeScheduleController::class, 'store']);
    // Route::delete('/workspaces/{workspace}/schedules/{schedule}', [CollaborativeScheduleController::class, 'destroy']);
    // Route::post('/workspaces/{workspace}/resources', [ResourceLinkController::class, 'store']);
    // Route::delete('/workspaces/{workspace}/resources/{resource}', [ResourceLinkController::class, 'destroy']);
    // Route::get('/workspaces/{workspace}/tasks/{task}', [CollaborativeTaskController::class, 'show']);
    // Route::patch('/workspaces/tasks/{task}/status', [CollaborativeTaskController::class, 'updateStatus']);
    // Route::post('/workspaces/{workspace}/tasks', [CollaborativeTaskController::class, 'store']);
    // Route::delete('/workspaces/{workspace}/tasks/{task}', [CollaborativeTaskController::class, 'destroy']);
});
