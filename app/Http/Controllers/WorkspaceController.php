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

        // GANTI DARI create() KE attach() 
        // Ini kuncinya agar tidak menyentuh tabel 'users'
        $workspace->members()->attach(auth()->id(), [
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('workspaces.show', ['workspace' => $workspace->id]);
    }

    // READ — Tampilkan workspace beserta anggotanya (FIX: Menggunakan Route Model Binding)
   // show()
public function show(Workspace $workspace)
{
    $workspace->load('members'); // ✅ hapus .user

    $tasks = CollaborativeTask::where('workspace_id', $workspace->id)->get();
    $schedules = CollaborativeSchedule::where('workspace_id', $workspace->id)->get();
    $resources = ResourceLink::where('workspace_id', $workspace->id)->get();

    $currentUserId = auth()->id();

    // ✅ firstWhere pakai 'id' bukan 'user_id', karena $member sudah User
    $member = $workspace->members->firstWhere('id', $currentUserId);

    // ✅ role ada di pivot
    $isOwner = ($workspace->owner_id == $currentUserId) || ($member && $member->pivot->role === 'admin');

    if (!$isOwner && !$member) {
        abort(403, 'Akses Ditolak: Kamu bukan anggota workspace ini.');
    }

    return view('workspaces.show', compact('workspace', 'tasks', 'schedules', 'resources', 'isOwner'));
}

// index()
public function index()
{
    $all = Workspace::whereHas('members', function ($query) {
        $query->where('user_id', auth()->id()); // ✅ ini tetap pakai user_id (query ke tabel pivot)
    })->with('members')->get();

    $myWorkspaces = $all->filter(function ($w) {
        $member = $w->members->firstWhere('id', auth()->id()); // ✅ ganti user_id → id
        return $member && $member->pivot->role === 'admin';    // ✅ pivot->role
    })->values();

    $sharedWorkspaces = $all->reject(function ($w) {
        $member = $w->members->firstWhere('id', auth()->id()); // ✅ ganti user_id → id
        return $member && $member->pivot->role === 'admin';    // ✅ pivot->role
    })->values();

    return view('workspaces.index', compact('myWorkspaces', 'sharedWorkspaces'));
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
        // 1. Validasi ditambah 'role' yang dikirim dari Modal HTML
        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:admin,collaborator'
        ]);

        $email = $request->input('email');
        $role = $request->input('role');
        
        $user = User::where('email', $email)->first();

        // Jika user belum pernah register ke aplikasimu
        if (!$user) {
            return back()->with('error', 'User dengan email tersebut tidak terdaftar di sistem.');
        }

        // 2. Cek apakah user tersebut sudah ada di tabel pivot workspace_members
        $member = $workspace->members()->where('user_id', $user->id)->first();

        if ($member) {
            // Jika SUDAH menjadi anggota, kita cukup update 'role'-nya saja
            $workspace->members()->updateExistingPivot($user->id, [
                'role'       => $role,
                'updated_at' => now(),
            ]);
        } else {
            // Jika BELUM menjadi anggota, gunakan attach() untuk memasukkan data ke tabel pivot
            $workspace->members()->attach($user->id, [
                'role'       => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Proses Pengiriman Email (Sesuai kodingan aslimu)
        try {
            // MATIKAN SEMENTARA BARIS INI SAMPAI TEMANMU MEMBUAT FILE-NYA
            // Mail::to($email)->send(new \App\Mail\WorkspaceInviteMail($workspace, $email));
            
            // Tambahkan log simulasi agar kita tahu sistemnya sebenarnya berjalan
            \Log::info("Simulasi undangan ($role) berhasil dikirim ke: " . $email);

        } catch (\Exception $e) {
            \Log::error('Failed to send workspace invite: '.$e->getMessage());
            return back()->with('error', 'Gagal mengirim undangan. Silakan coba lagi.');
        }

        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', "Berhasil! Pengguna diundang sebagai $role.");
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