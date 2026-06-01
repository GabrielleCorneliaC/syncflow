<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace; 
use Illuminate\Support\Facades\Http;

class WorkspaceController extends Controller
{
    // READ — Tampilkan daftar semua workspace di halaman utama
    public function index() 
    {
        // Hanya ambil data workspace di mana user yang sedang login menjadi anggotanya
        $workspaces = Workspace::whereHas('members', function($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return view('workspaces.index', compact('workspaces'));
    }

    // CREATE — Buat workspace baru, otomatis jadikan creator sebagai Admin
    public function store(Request $request) 
    {
        $workspace = Workspace::create([
            'name'        => $request->name,
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
    public function show($workspace_id) // <--- Ubah $id jadi $workspace_id
    {
        $workspace = Workspace::with('members')->findOrFail($workspace_id);
        
        return view('workspaces.show', compact('workspace')); // <-- pakai 's'
    }

    // UPDATE — Edit nama atau cover
    public function update(Request $request, $workspace_id) // <--- Ubah $id jadi $workspace_id
    {
        $workspace = Workspace::findOrFail($workspace_id);
        $workspace->update($request->only(['name', 'cover_image']));
        
        return back()->with('success', 'Workspace diperbarui.');
    }

    // DELETE — Hanya admin (sudah dijaga middleware)
    public function destroy($workspace_id) // <--- Ubah $id jadi $workspace_id
    {
        Workspace::findOrFail($workspace_id)->delete();
        
        // Redirect ke workspaces.index (pakai 's')
        return redirect()->route('workspaces.index');
    }

    // INVITE anggota baru
    public function invite(Request $request, $workspace_id) 
    {
        $workspace = Workspace::findOrFail($workspace_id);
        
        // Karena relasinya hasMany, kita pakai create() (bukan attach)
        $workspace->members()->create([
            'user_id' => $request->user_id,
            'role'    => 'collaborator',
        ]);
        
        return back()->with('success', 'Anggota berhasil diundang.');
    }

    // Proxy API Unsplash
    public function getUnsplashImages(Request $request) 
    {
        $query    = $request->input('query', 'workspace'); 
        $apiKey   = env('UNSPLASH_ACCESS_KEY');

        $response = Http::get("https://api.unsplash.com/search/photos", [
            'query'       => $query,
            'per_page'    => 12,
            'client_id'   => $apiKey,
        ]);

        $images = $response->json()['results'] ?? [];

        // Ambil hanya URL yang dibutuhkan
        $urls = collect($images)->map(fn($img) => [
            'thumb'   => $img['urls']['thumb'],
            'regular' => $img['urls']['regular'],
        ]);

        return response()->json($urls);
    }
}