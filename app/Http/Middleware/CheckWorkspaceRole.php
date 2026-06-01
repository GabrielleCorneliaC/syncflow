<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth; // Di-comment dulu karena login belum jadi
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspaceRole
{
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        // 1. Matikan pengecekan login sementara (Bypass untuk testing)
        // if (!Auth::check()) {
        //     return redirect('/login');
        // }

        // 2. Ambil parameter ID dari URL (Sesuai dengan nama rute {workspace_id})
        $workspaceId = $request->route('workspace_id');

        // 3. Cek di tabel pivot (Pakai user_id = 1 sebagai dummy)
        $member = DB::table('workspace_members')
                    ->where('workspace_id', $workspaceId)
                    ->where('user_id', 1) // <--- Bypass Auth::id() menjadi angka 1
                    ->first();

        // 4. Kalau bukan member sama sekali, tolak dan kembalikan ke daftar workspace
        if (!$member) {
            return redirect('/workspaces')->with('error', 'Akses Ditolak: Kamu bukan anggota workspace ini.');
        }

        // 5. Kalau rute butuh role Admin tapi role dia bukan 'admin', tolak
        if ($role === 'admin' && $member->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.');
        }

        // Jika aman, persilakan masuk
        return $next($request);
    }
}