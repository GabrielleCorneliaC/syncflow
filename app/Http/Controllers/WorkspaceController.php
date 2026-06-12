<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkspaceInvite as WorkspaceInviteMail;

class WorkspaceController extends Controller
{
    // READ — Tampilkan daftar semua workspace di halaman utama
    public function index() 
    {
        // Ambil semua workspace di mana user saat ini menjadi anggota
        $all = Workspace::whereHas('members', function($query) {
            $query->where('user_id', auth()->id());
        })->with('members.user')->get();

        // Pisahkan menjadi milik saya (admin) dan yang dibagikan ke saya (collaborator)
        $myWorkspaces = $all->filter(function($w) {
            $m = $w->members->firstWhere('user_id', auth()->id());
            return $m && $m->role === 'admin';
        })->values();

        $sharedWorkspaces = $all->reject(function($w) {
            $m = $w->members->firstWhere('user_id', auth()->id());
            return $m && $m->role === 'admin';
        })->values();

        return view('workspaces.index', compact('myWorkspaces', 'sharedWorkspaces'));
    }

    // CREATE — Buat workspace baru, otomatis jadikan creator sebagai Admin
    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|url',
        ]);

        $workspace = Workspace::create([
            'name'        => $request->name,
            'description' => $request->description,
            'cover_image' => $request->cover_image,
        ]);

        // Karena relasinya hasMany, kita pakai create() (bukan attach)
        $workspace->members()->create([
            'user_id' => auth()->id(),
            'role'    => 'admin',
        ]);

        // Redirect ke workspaces.show (pakai 's') dan sertakan nama parameternya
        return redirect()->route('workspaces.show', ['workspace_id' => $workspace->id]);
    }

    // READ — Tampilkan workspace beserta anggotanya
    public function show(int $workspace_id)
    {
        $workspace = Workspace::with('members.user')->findOrFail($workspace_id);

        $tasks = collect();
        $schedules = collect();
        $resources = collect();

        // Determine if current user is an admin (owner) in this workspace
        $member = $workspace->members->firstWhere('user_id', auth()->id());
        $isOwner = $member && $member->role === 'admin';

        return view('workspaces.show', compact('workspace', 'tasks', 'schedules', 'resources', 'isOwner'));
    }
    // UPDATE — Edit nama atau cover
    public function update(Request $request, int $workspace_id) // <--- Ubah $id jadi $workspace_id
    {
        $workspace = Workspace::findOrFail($workspace_id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|url',
        ]);
        $workspace->update($request->only(['name', 'cover_image', 'description']));
        
        return back()->with('success', 'Workspace diperbarui.');
    }

    // DELETE — Hanya admin (sudah dijaga middleware)
    public function destroy(int $workspace_id) // <--- Ubah $id jadi $workspace_id
    {
        Workspace::findOrFail($workspace_id)->delete();
        
        // Redirect ke workspaces.index (pakai 's')
        return redirect()->route('workspaces.index');
    }

    // INVITE anggota baru
    public function invite(Request $request, int $workspace_id)
    {
        $request->validate(['email' => 'required|email']);
        $workspace = Workspace::findOrFail($workspace_id);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            if (! $workspace->members()->where('user_id', $user->id)->exists()) {
                $workspace->members()->create([
                    'user_id' => $user->id,
                    'role'    => 'collaborator',
                ]);
            }
        }

        try {
            Mail::to($email)->send(new WorkspaceInviteMail($workspace, $email));
        } catch (\Exception $e) {
            \Log::error('Failed to send workspace invite: '.$e->getMessage());
            return back()->with('error', 'Gagal mengirim undangan. Silakan coba lagi.');
        }

        return back()->with('success', 'Undangan berhasil dikirim ke: ' . $email);
    }

    // Proxy API Unsplash
   public function unsplashSearch(Request $request)
{
    $query = $request->input('query', 'workspace');
    $apiKey = env('UNSPLASH_ACCESS_KEY');

    if (!$apiKey) {
        return response()->json([
            'error' => 'Unsplash API key not configured'
        ], 500);
    }

    try {
        $response = Http::withoutVerifying()->get(
            'https://api.unsplash.com/search/photos',
            [
                'query' => $query,
                'per_page' => 12,
                'client_id' => $apiKey,
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'error' => 'Unsplash API error'
            ], $response->status());
        }

        $images = $response->json()['results'] ?? [];

        $results = collect($images)->map(function ($img) {
            return [
                'urls' => [
                    'small'   => $img['urls']['small'] ?? $img['urls']['thumb'],
                    'regular' => $img['urls']['regular'],
                    'thumb'   => $img['urls']['thumb'],
                ],
                'alt_description' => $img['alt_description'] ?? '',
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
}
