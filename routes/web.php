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

// ─────────────────────────────────────────────────────────
//  GUEST ROUTES (hanya bisa diakses kalau BELUM login)
// ─────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {

    // ── Email / Password Auth ─────────────────────────────
    Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AuthController::class, 'login'])->name('login.store');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    // ── Google OAuth ──────────────────────────────────────
    // Step 1: redirect ke Google consent screen
    Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    // Step 2: callback dari Google setelah user mengizinkan
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// ─────────────────────────────────────────────────────────
//  AUTHENTICATED ROUTES (harus login dulu)
// ─────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Root redirect ke dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Profil ────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',             [ProfileController::class, 'show'])->name('show');
        Route::patch('/info',       [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::patch('/password',   [ProfileController::class, 'updatePassword'])->name('update.password');
        Route::post('/avatar',      [ProfileController::class, 'updateAvatar'])->name('update.avatar');
        Route::delete('/delete',    [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ── Admin ─────────────────────────────────────────────
    Route::prefix('admin/users')->name('admin.users.')->middleware('can:admin')->group(function () {
        Route::get('/',         [ProfileController::class, 'index'])->name('index');
        Route::delete('/{user}',[ProfileController::class, 'adminDestroy'])->name('destroy');
    });


/*
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
    */
});