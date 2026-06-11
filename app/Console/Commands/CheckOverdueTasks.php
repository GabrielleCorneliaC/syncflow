<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollaborativeTask;
use Carbon\Carbon;

class CheckOverdueTasks extends Command
{
    // Nama perintah yang nanti dipanggil oleh sistem
    protected $signature = 'tasks:check-overdue';

    // Deskripsi perintah
    protected $description = 'Mengecek dan mengubah status tugas yang melewati tenggat waktu menjadi overdue';

    public function handle()
    {
        // Cari tugas yang due_date-nya sudah lewat dari waktu sekarang,
        // dan statusnya BUKAN completed atau overdue.
        $tasksToUpdate = CollaborativeTask::whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->whereNotIn('status', ['completed', 'overdue'])
            ->update(['status' => 'overdue']);

        // Menampilkan pesan sukses di terminal log
        $this->info("Berhasil memperbarui {$tasksToUpdate} tugas menjadi overdue.");
    }
}