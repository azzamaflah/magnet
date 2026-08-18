<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Magang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$now = Carbon::now()->toDateString();

// Group by asal_kampus with count of aktif, selesai, and total
$kampusStats = Magang::select('asal_kampus',
    DB::raw("SUM(CASE WHEN tanggal_mulai <= '{$now}' AND tanggal_selesai >= '{$now}' THEN 1 ELSE 0 END) as sedang_magang"),
    DB::raw("SUM(CASE WHEN tanggal_selesai < '{$now}' THEN 1 ELSE 0 END) as selesai_magang"),
    DB::raw("SUM(CASE WHEN tanggal_mulai > '{$now}' THEN 1 ELSE 0 END) as belum_mulai"),
    DB::raw("COUNT(*) as total_peserta")
)
->groupBy('asal_kampus')
->orderByDesc('total_peserta')
->get();

echo "Kampus Statistics:\n";
foreach ($kampusStats as $stat) {
    echo "• {$stat->asal_kampus} => Sedang: {$stat->sedang_magang}, Selesai: {$stat->selesai_magang}, Belum Mulai: {$stat->belum_mulai}, Total: {$stat->total_peserta}\n";
}
