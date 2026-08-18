<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$magangs = App\Models\Magang::all(['id', 'nama', 'foto']);
echo "Total Magangs: " . $magangs->count() . "\n";
foreach ($magangs as $m) {
    $publicPath = public_path('storage/' . $m->foto);
    $storagePath = storage_path('app/public/' . $m->foto);
    $existsPublic = file_exists($publicPath) ? 'YES' : 'NO';
    $existsStorage = file_exists($storagePath) ? 'YES' : 'NO';
    echo "ID: {$m->id} | Nama: {$m->nama} | Foto DB: '{$m->foto}' | In public/storage: {$existsPublic} | In storage/app/public: {$existsStorage}\n";
}
