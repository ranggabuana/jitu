<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$perijinan = \App\Models\Perijinan::findOrFail(25);
echo "NAMA PERIZINAN: " . $perijinan->nama_perijinan . "\n";
echo "template_surat_rekom (column name):\n";
echo $perijinan->template_surat_rekom . "\n";
echo "----------------------------------------\n";
echo "template_surat_izin (column name):\n";
echo $perijinan->template_surat_izin . "\n";
echo "----------------------------------------\n";

$opdConfigs = \App\Models\PerijinanOpdConfig::where('perijinan_id', 25)->get();
foreach ($opdConfigs as $cfg) {
    echo "OPD ID: " . $cfg->opd_id . " (" . ($cfg->opd->nama_opd ?? 'N/A') . ")\n";
    echo "template_surat_rekom:\n";
    echo $cfg->template_surat_rekom . "\n";
    echo "----------------------------------------\n";
}
