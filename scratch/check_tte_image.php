<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$opd = \App\Models\Opd::findOrFail(26);
echo "OPD Name: " . $opd->nama_opd . "\n";
echo "gambar_tte path: " . $opd->gambar_tte . "\n";
if ($opd->gambar_tte) {
    $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($opd->gambar_tte);
    echo "Exists in public storage disk? " . ($exists ? 'YES' : 'NO') . "\n";
    if ($exists) {
        $absPath = \Illuminate\Support\Facades\Storage::disk('public')->path($opd->gambar_tte);
        echo "Storage path: " . $absPath . "\n";
        echo "File size: " . filesize($absPath) . " bytes\n";
    }
}
