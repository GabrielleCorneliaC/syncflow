<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspaceRole
{
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        // 1. Ambil parameter dari URL rute
        $workspace = $request->route('workspace');
        $workspaceId = $workspace instanceof \App\Models\Workspace ? $workspace->id : $workspace;

        // Jika $workspaceId kosong (kasus centang tugas / AJAX), cari workspace_id lewat Task/Schedule
        if (!$workspaceId && $request->route('task')) {
            $taskParam = $request->route('task');
            
            if ($taskParam instanceof \App\Models\CollaborativeTask) {
                $workspaceId = $taskParam->workspace_id;
            } else {
                $workspaceId = DB::table('collaborative_schedules')
                    ->where('id', $taskParam)
                    ->value('workspace_id') 
                    ?? 
                    DB::table('collaborative_tasks')
                    ->where('id', $taskParam)
                    ->value('workspace_id');
            }
        }

        // Ambil ID user yang sedang login saat ini (Dinamis, bukan angka 2 lagi!)
        $currentUserId = auth()->id();

        // 2. Ambil data asli workspace untuk cek Owner Utama
        $workspaceData = DB::table('workspaces')->where('id', $workspaceId)->first();
        $isOwner = $workspaceData && ($workspaceData->owner_id == $currentUserId);

        // 3. Cek data di tabel pivot anggota
        $member = DB::table('workspace_members')
                    ->where('workspace_id', $workspaceId)
                    ->where('user_id', $currentUserId) // 💡 SELESAI: Sudah dinamis mengikuti user yang login
                    ->first();

        // 4. Kalau BUKAN owner DAN JUGA BUKAN member sama sekali, tolak!
        if (!$isOwner && !$member) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Kamu bukan anggota workspace ini.'], 403);
            }

            return redirect('/workspaces')->with('error', 'Akses Ditolak: Kamu bukan anggota workspace ini.');
        }

        // 5. Kalau rute butuh role Admin, tapi dia bukan Owner utama DAN di tabel member juga bukan 'admin', tolak!
        $hasAdminAccess = $isOwner || ($member && $member->role === 'admin');
        
        if ($role === 'admin' && !$hasAdminAccess) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.'], 403);
            }
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.');
        }

        return $next($request);
    }
}