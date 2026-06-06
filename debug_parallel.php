<?php
require 'vendor/autoload.php';
// Boot Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\DataPerijinan;
use App\Models\User;

// Find a multi-OPD perijinan application
$application = DataPerijinan::whereHas('perijinan', function($q) {
    $q->where('is_multi_opd', true);
})->with(['perijinan', 'validasiRecords.validationFlow.assignedUser.opd'])->latest()->first();

if (!$application) {
    echo "No multi-OPD application found.\n";
    exit;
}

echo "APP ID: " . $application->id . " (" . $application->no_registrasi . ")\n";
echo "STATUS: " . $application->status . " | CURRENT STEP: " . $application->current_step . "\n";
echo "IS MULTI-OPD: " . ($application->perijinan->is_multi_opd ? 'YES' : 'NO') . "\n";

echo "\nVALIDATION RECORDS:\n";
foreach ($application->validasiRecords->sortBy('order') as $v) {
    $flow = $v->validationFlow;
    $assigned = $flow->assignedUser ?? null;
    $opd = $assigned->opd ?? null;
    
    echo sprintf(
        "Order %d: Role %s | Status %s | Assigned: %s (%s) | OPD: %s\n",
        $v->order,
        $flow->role ?? 'N/A',
        $v->status,
        $assigned->name ?? 'None',
        $assigned->role ?? 'None',
        $opd->nama_opd ?? 'None'
    );
}

// Logic Check: Simulate Operator OPD B check
// Find the first step that has 'operator_opd' or 'kepala_opd'
$opdSteps = $application->validasiRecords->filter(function($v) {
    return $v->validationFlow && in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']);
});

$minOpdOrder = $opdSteps->min('order');
echo "\nMIN OPD ORDER: " . ($minOpdOrder ?? 'NONE') . "\n";
echo "REACHED OPD PHASE? " . ($application->current_step >= $minOpdOrder ? 'YES' : 'NO') . "\n";

// Check for each user in OPD roles if they should be able to validate
foreach ($opdSteps as $v) {
    $user = $v->validationFlow->assignedUser;
    if (!$user) continue;
    
    $isParallelOpdTurn = false;
    if ($application->current_step >= $minOpdOrder) {
        if ($user->role === 'kepala_opd') {
            $myOp = $application->validasiRecords->first(function($vr) use ($user) {
                return $vr->validationFlow && $vr->validationFlow->role === 'operator_opd' && 
                       $vr->validationFlow->assignedUser && $vr->validationFlow->assignedUser->opd_id == $user->opd_id;
            });
            $isParallelOpdTurn = $myOp && $myOp->status === 'approved';
        } else {
            $isParallelOpdTurn = true;
        }
    }
    
    echo sprintf(
        "User %s (OPD: %s): Parallel Turn? %s\n",
        $user->name,
        $user->opd->nama_opd ?? 'None',
        $isParallelOpdTurn ? 'YES' : 'NO'
    );
}
