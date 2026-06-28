<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$perijinan = \App\Models\Perijinan::findOrFail(25);
echo "NAMA PERIZINAN: " . $perijinan->nama_perijinan . "\n";
echo "SURAT REKOM TEMPLATE:\n";
echo $perijinan->surat_rekom_template . "\n";
echo "----------------------------------------\n";
echo "SURAT IZIN TEMPLATE:\n";
echo $perijinan->surat_izin_template . "\n";
