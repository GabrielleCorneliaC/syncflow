<?php

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Controllers\CollaborativeTaskController;
use App\Http\Controllers\CollaborativeScheduleController;
use Illuminate\Support\Facades\Route;

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
    return redirect('/workspaces');
});
// =========================================================================


// ─────────────────────────────────────────────────────────────────────────────
// SEMUA ROUTE DI BAWAH INI DILINDUNGI auth (harus login)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

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
        Route::patch('/workspaces/{workspace_id}', [WorkspaceController::class, 'update'])->name('workspaces.update');
        Route::post('/workspaces/{workspace_id}/members', [WorkspaceController::class, 'invite'])->name('workspaces.members.store');
    });


    // ─────────────────────────────────────────────────────────────────────────
    // GROUP: COLLABORATIVE FEATURES (Tasks, Schedules, Resources)
    // ─────────────────────────────────────────────────────────────────────────
    // Route::post('/workspaces/{workspace_id}/tasks', [CollaborativeTaskController::class, 'store'])->name('workspaces.tasks.store');
    // Route::get('/workspaces/{workspace_id}/tasks/{task_id}', [CollaborativeTaskController::class, 'show'])->name('workspaces.tasks.show');
    // Route::patch('/workspaces/tasks/{task_id}/status', [CollaborativeTaskController::class, 'updateStatus'])->name('workspaces.tasks.updateStatus');
    // Route::delete('/workspaces/{workspace_id}/tasks/{task_id}', [CollaborativeTaskController::class, 'destroy'])->name('workspaces.tasks.destroy');

    // Route::post('/workspaces/{workspace_id}/schedules', [CollaborativeScheduleController::class, 'store'])->name('workspaces.schedules.store');
    // Route::delete('/workspaces/{workspace_id}/schedules/{schedule_id}', [CollaborativeScheduleController::class, 'destroy'])->name('workspaces.schedules.destroy');

});
