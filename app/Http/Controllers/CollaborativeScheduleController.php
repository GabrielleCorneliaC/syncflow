<?php

namespace App\Http\Controllers;

use App\Models\CollaborativeSchedule;
use Illuminate\Http\Request;

class CollaborativeScheduleController extends Controller
{
    // Menyimpan jadwal baru
    public function store(Request $request, $workspace_id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after_or_equal:start_time', // Validasi: end_time gak boleh mendahului start_time
        ]);

        CollaborativeSchedule::create([
            'workspace_id' => $workspace_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
        ]);

        return back()->with('success', 'Jadwal kolaborasi berhasil ditambahkan!');
    }

    // Menghapus jadwal
    public function destroy($workspace_id, $id)
    {
        // Cari jadwal berdasarkan ID dan pastikan berada di workspace yang tepat
        $schedule = CollaborativeSchedule::where('workspace_id', $workspace_id)->findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}