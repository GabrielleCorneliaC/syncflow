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
        $workspace = $request->route('workspace') ?? $request->input('workspace');
        $workspaceId = $workspace instanceof \App\Models\Workspace ? $workspace->id : $workspace;

        if (!$workspaceId) {
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

        $currentUserId = auth()->id();

        $member = DB::table('workspace_members')
                    ->where('workspace_id', $workspaceId)
                    ->where('user_id', $currentUserId)
                    ->first();

        if (!$member) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Kamu bukan anggota workspace ini.'], 403);
            }
            return redirect('/workspaces')->with('error', 'Akses Ditolak: Kamu bukan anggota workspace ini.');
        }

        if ($role === 'admin' && $member->role !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.'], 403);
            }
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya Admin yang bisa melakukan ini.');
        }

        return $next($request);
    }
}