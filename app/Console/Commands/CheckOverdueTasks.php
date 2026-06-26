<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollaborativeTask;
use Carbon\Carbon;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';

    protected $description = 'Mengecek dan mengubah status task yang lewat deadline menjadi overdue';

    public function handle()
    {
        $overdueTasks = CollaborativeTask::whereNotIn('status', ['done', 'overdue'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        $this->info("Berhasil mengupdate {$overdueTasks} task menjadi overdue.");
    }
}