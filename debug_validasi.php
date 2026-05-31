<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataPerijinan;

$application = DataPerijinan::with(['validasiRecords.validationFlow'])->latest()->first();

if (!$application) {
    echo "No application found.\n";
    exit;
}

echo "Application: " . $application->no_registrasi . " (ID: " . $application->id . ")\n";
echo "Current Step: " . $application->current_step . "\n";
echo "Status: " . $application->status . "\n";
echo "-----------------------------------\n";

foreach ($application->validasiRecords as $v) {
    echo "Order: " . $v->order . " | Status: " . $v->status . " | Role: " . ($v->validationFlow->role ?? 'N/A') . " | User ID: " . ($v->user_id ?? 'NULL') . "\n";
}
