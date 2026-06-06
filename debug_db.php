<?php
require 'vendor/autoload.php';
// Boot Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\DataPerijinan;
use App\Models\Perijinan;

// List all perijinan and their is_multi_opd status
$list = Perijinan::all(['id', 'nama_perijinan', 'is_multi_opd']);
foreach ($list as $p) {
    echo "ID: " . $p->id . " | Name: " . $p->nama_perijinan . " | Multi-OPD: " . ($p->is_multi_opd ? 'YES' : 'NO') . "\n";
}

$apps = DataPerijinan::latest()->take(5)->get(['id', 'no_registrasi', 'perijinan_id', 'status', 'current_step']);
echo "\nRECENT APPLICATIONS:\n";
foreach ($apps as $a) {
    echo "ID: " . $a->id . " | Reg: " . $a->no_registrasi . " | Perijinan ID: " . $a->perijinan_id . " | Status: " . $a->status . "\n";
}
