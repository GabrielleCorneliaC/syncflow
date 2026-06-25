<?php

namespace App\Http\Controllers;

use App\Models\ResourceLink;
use Illuminate\Http\Request;

class ResourceLinkController extends Controller
{
    // Menyimpan link referensi baru
    public function store(Request $request, $workspace_id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'required|url', // Memastikan input benar-benar format link (http/https)
            'description' => 'nullable|string',
        ]);

        ResourceLink::create([
            'workspace_id' => $workspace_id,
            'title'        => $request->title,
            'url'          => $request->url,
            'description'  => $request->description,
        ]);

        return back()->with('success', 'Link referensi berhasil ditambahkan!');
    }

    public function update(Request $request, $workspace_id, $resource)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'required|url',
            'description' => 'nullable|string',
        ]);

        $resource = ResourceLink::where('workspace_id', $workspace_id)->findOrFail($resource);

        $resource->update([
            'title'       => $request->title,
            'url'         => $request->url,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Link referensi berhasil diperbarui!');
    }

    // Menghapus link referensi
    public function destroy($workspace_id, $resource)
    {
        $resource = ResourceLink::where('workspace_id', $workspace_id)->findOrFail($resource);
        $resource->delete();

        return back()->with('success', 'Link referensi berhasil dihapus!');
    }
}