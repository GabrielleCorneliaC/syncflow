<?php

namespace App\Http\Controllers;

use App\Models\CollaborativeTask;
use Illuminate\Http\Request;

class CollaborativeTaskController extends Controller
{
    // Fungsi untuk menyimpan tugas baru ke dalam Workspace
    public function store(Request $request, $workspace_id)
    {
        // Validasi input dari form
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        // Simpan data ke database
        CollaborativeTask::create([
            'workspace_id' => $workspace_id, // Didapat dari parameter URL rute
            'title'        => $request->title,
            'description'  => $request->description,
            'assignee_id'  => $request->assignee_id,
            'due_date'     => $request->due_date,
            'status'       => 'pending', // Status default
        ]);

        // Kembalikan ke halaman workspace halaman sebelumnya
        return back()->with('success', 'Tugas berhasil ditambahkan ke dalam Workspace!');
    }

    // Fungsi AJAX untuk update status centang
    public function updateStatusAjax(Request $request, $id)
    {
        // Validasi input status yang dikirim JavaScript
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,overdue'
        ]);

        // Cari tugas berdasarkan ID
        $task = CollaborativeTask::findOrFail($id);
        
        // Update dan simpan
        $task->status = $request->status;
        $task->save();

        // Kembalikan response berupa JSON ke JavaScript frontend
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah menjadi ' . $task->status,
            'new_status' => $task->status
        ]);
    }

    // Hapus tugas dari workspace
    public function destroy($workspace_id, $id)
    {
        // Cari tugas berdasarkan ID
        $task = CollaborativeTask::where('workspace_id', $workspace_id)->findOrFail($id);
        
        // Eksekusi penghapusan data
        $task->delete();

        // Kembalikan ke halaman sebelumnya
        return back()->with('success', 'Tugas berhasil dihapus dari Workspace!');
    }
}