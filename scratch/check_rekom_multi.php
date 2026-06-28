<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestApp = \App\Models\DataPerijinan::where('perijinan_id', 25)
    ->orderBy('id', 'desc')
    ->first();

if ($latestApp) {
    echo "Latest Application ID: " . $latestApp->id . "\n";
    echo "No Registrasi: " . $latestApp->no_registrasi . "\n";
    echo "Status: " . $latestApp->status . "\n";
    echo "Current Step: " . $latestApp->current_step . "\n";
    echo "Rekom Data Multi: " . json_encode($latestApp->rekom_data_multi, JSON_PRETTY_PRINT) . "\n";
    echo "BO Data: " . json_encode($latestApp->bo_data, JSON_PRETTY_PRINT) . "\n";
    echo "----------------------------------------\n";
    
    // Check active validation records
    $records = $latestApp->validasiRecords()->with('validationFlow.assignedUser.opd')->get();
    foreach ($records as $r) {
        $flow = $r->validationFlow;
        $opdName = $flow && $flow->assignedUser && $flow->assignedUser->opd 
            ? $flow->assignedUser->opd->nama_opd 
            : 'N/A';
        echo "Order: " . $r->order . " | Role: " . ($flow->role ?? 'N/A') . " | OPD: " . $opdName . " | Status: " . $r->status . "\n";
    }
} else {
    echo "No application found for Perijinan 25\n";
}
