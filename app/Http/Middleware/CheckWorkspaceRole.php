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
        // 1. Ambil ID Workspace dari Route ATAU dari Query Parameter (?workspace=2)
        $workspace = $request->route('workspace') ?? $request->input('workspace');
        $workspaceId = $workspace instanceof \App\Models\Workspace ? $workspace->id : $workspace;

        // Jika mengakses lewat Task / Schedule (AJAX status checkbox / komentar)
        if (!$workspaceId) {
            // Ambil ID task dari Route ATAU dari Query Parameter (?task=2)
            $taskParam = $request->route('task') ?? $request->input('task');
            
            if ($taskParam) {
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
        }

        // 2. Ambil ID user yang sedang login saat ini (Sudah dinamis!)
        $currentUserId = auth()->id();

        // 3. Cek apakah user ini terdaftar sebagai anggota di workspace ini
        $member = DB::table('workspace_members')
                    ->where('workspace_id', $workspaceId)
                    ->where('user_id', $currentUserId)
                    ->first();

        // 4. Kalau namanya GAK ADA di tabel member, langsung TOLAK!
        if (!$member) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Kamu bukan anggota workspace ini.'], 403);
            }
            return redirect('/workspaces')->with('error', 'Akses Ditolak: Kamu bukan anggota workspace ini.');
        }

        // 5. Kalau rute butuh hak 'admin', pastikan kolom role di tabel member bernilai 'admin'
        if ($role === 'admin' && $member->role !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.'], 403);
            }
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.');
        }

        return $next($request);
    }
}