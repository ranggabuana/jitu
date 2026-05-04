<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PerijinanValidationFlow;
use App\Models\DataPerijinanValidasi;

echo "=== FO/BO Validation Flows ===\n";
$flows = PerijinanValidationFlow::whereIn('role', ['fo', 'bo'])->get();
echo "Count: " . $flows->count() . "\n";
foreach ($flows as $f) {
    echo "  flow id={$f->id} perijinan_id={$f->perijinan_id} role={$f->role} assigned_user_id=" . ($f->assigned_user_id ?? 'NULL') . "\n";
}

echo "\n=== DataPerijinanValidasi Records ===\n";
$records = DataPerijinanValidasi::with('validationFlow')->get();
echo "Count: " . $records->count() . "\n";
foreach ($records as $v) {
    $role = $v->validationFlow ? $v->validationFlow->role : 'NO_FLOW';
    $flowAssigned = $v->validationFlow ? ($v->validationFlow->assigned_user_id ?? 'NULL') : 'NO_FLOW';
    echo "  validasi id={$v->id} order={$v->order} role={$role} user_id=" . ($v->user_id ?? 'NULL') . " flow.assigned_user_id={$flowAssigned}\n";
}

echo "\n=== Users with role fo or bo ===\n";
$users = \App\Models\User::whereIn('role', ['fo', 'bo'])->get(['id', 'name', 'role', 'status']);
foreach ($users as $u) {
    echo "  id={$u->id} name={$u->name} role={$u->role} status={$u->status}\n";
}
