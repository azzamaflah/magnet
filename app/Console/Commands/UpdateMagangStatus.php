<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Magang;

class UpdateMagangStatus extends Command
{
    protected $signature = 'magang:update-status';
    protected $description = 'Update status semua data magang berdasarkan tanggal';

    public function handle()
    {
        $this->info('Memulai update status magang...');

        $magangs = Magang::all();
        $updated = 0;

        foreach ($magangs as $magang) {
            $oldStatus = $magang->status;
            $magang->updateStatus();
            $magang->save();

            if ($oldStatus !== $magang->status) {
                $updated++;
                $this->line("✅ {$magang->nama}: {$oldStatus} → {$magang->status}");
            }
        }

        $this->info("Selesai! {$updated} data berhasil diupdate.");
        return 0;
    }
}
