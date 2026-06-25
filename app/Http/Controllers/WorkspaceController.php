<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\CollaborativeTask;
use App\Models\CollaborativeSchedule;
use App\Models\ResourceLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkspaceInvite as WorkspaceInviteMail;

class WorkspaceController extends Controller
{
    // READ — Tampilkan daftar semua workspace di halaman utama
    public function index() 
    {
        // Ambil semua workspace di mana user saat ini menjadi anggota
        $all = Workspace::whereHas('members', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('members.user')->get();

        // Pisahkan menjadi milik saya (admin) dan yang dibagikan ke saya (collaborator)
        $myWorkspaces = $all->filter(function ($w) {
            $member = $w->members->firstWhere('user_id', auth()->id());
            return $member && $member->role === 'admin';
        })->values();

        $sharedWorkspaces = $all->reject(function ($w) {
            $member = $w->members->firstWhere('user_id', auth()->id());
            return $member && $member->role === 'admin';
        })->values();

        return view('workspaces.index', compact('myWorkspaces', 'sharedWorkspaces'));
    }

    // CREATE — Simpan workspace baru
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

        // FIX: Diubah ke 'workspace' agar cocok dengan Route web.php baru
        return redirect()->route('workspaces.show', ['workspace' => $workspace->id]);
    }

    // READ — Tampilkan workspace beserta anggotanya (FIX: Menggunakan Route Model Binding)
   public function show(Workspace $workspace)
    {
        // 1. Ambil data asli dari temanmu
        $workspace->load('members.user');

        $tasks = CollaborativeTask::where('workspace_id', $workspace->id)->get();
        $schedules = CollaborativeSchedule::where('workspace_id', $workspace->id)->get();
        $resources = ResourceLink::where('workspace_id', $workspace->id)->get();

        $currentUserId = auth()->id();
        $member = $workspace->members->firstWhere('user_id', $currentUserId);

        // 💡 LOGIKA TERBAIK & PALING COCOK: 
        // User dianggap "Owner/Admin" jika dia pembuat Workspace (owner_id) ATAU terdaftar sebagai 'admin' di tabel member.
        $isOwner = ($workspace->owner_id == $currentUserId) || ($member && $member->role === 'admin');

        // 2. Pengaman Akses: Jika dia bukan pembuat dan namanya tidak terdaftar sama sekali di member, tendang!
        if (!$isOwner && !$member) {
            abort(403, 'Akses Ditolak: Kamu bukan anggota workspace ini.');
        }

        // Tetap kirim variabel '$isOwner' yang sama persis seperti kode lama temanmu
        return view('workspaces.show', compact('workspace', 'tasks', 'schedules', 'resources', 'isOwner'));
    }

    // UPDATE — Edit nama atau cover (FIX: Menggunakan Route Model Binding)
    public function update(Request $request, Workspace $workspace)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|url',
        ]);
        
        $workspace->update($request->only(['name', 'cover_image', 'description']));
        
        return back()->with('success', 'Workspace diperbarui.');
    }

    // DELETE — Hanya admin (FIX: Menggunakan Route Model Binding)
    public function destroy(Workspace $workspace)
    {
        $workspace->delete();
        
        return redirect()->route('workspaces.index');
    }

    // INVITE anggota baru (FIX: Menggunakan Route Model Binding)
    public function invite(Request $request, Workspace $workspace)
    {
        $request->validate(['email' => 'required|email']);

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
            // MATIKAN SEMENTARA BARIS INI SAMPAI TEMANMU MEMBUAT FILE-NYA
            // Mail::to($request->email)->send(new \App\Mail\WorkspaceInviteMail($workspace, $request->email));
            
            // Tambahkan log simulasi agar kita tahu sistemnya sebenarnya berjalan
            \Log::info('Simulasi undangan berhasil dikirim ke: ' . $request->email);

        } catch (\Exception $e) {
            \Log::error('Failed to send workspace invite: '.$e->getMessage());
            return back()->with('error', 'Gagal mengirim undangan. Silakan coba lagi.');
        }

        return back();
    }

    // Proxy API Unsplash
    public function unsplashSearch(Request $request)
    {
        $query = $request->input('query', 'workspace');
        $apiKey = env('UNSPLASH_ACCESS_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'Unsplash API key not configured'], 500);
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
                return response()->json(['error' => 'Unsplash API error'], $response->status());
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

            return response()->json(['results' => $results]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}