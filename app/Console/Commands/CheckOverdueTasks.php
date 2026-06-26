<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollaborativeTask;
use Carbon\Carbon;

class CheckOverdueTasks extends Command
{
    // Nama perintah untuk dijalankan di terminal
    protected $signature = 'tasks:check-overdue';

    // Deskripsi perintah
    protected $description = 'Mengecek dan mengubah status task yang lewat deadline menjadi overdue';

    public function handle()
    {
        // Cari task yang belum selesai, punya deadline, dan waktunya sudah lewat dari detik ini
        $overdueTasks = CollaborativeTask::whereNotIn('status', ['done', 'overdue'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        $this->info("Berhasil mengupdate {$overdueTasks} task menjadi overdue.");
    }
}