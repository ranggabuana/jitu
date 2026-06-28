<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestApp = \App\Models\DataPerijinan::findOrFail(57);
$path = $latestApp->rekom_data['foto_dokumentasi'] ?? null;
if ($path) {
    $absPath = public_path($path);
    echo "Path from DB: " . $path . "\n";
    echo "Absolute path: " . $absPath . "\n";
    echo "File exists? " . (file_exists($absPath) ? 'YES' : 'NO') . "\n";
    if (file_exists($absPath)) {
        echo "File size: " . filesize($absPath) . " bytes\n";
    }
} else {
    echo "No foto_dokumentasi in rekom_data\n";
}
