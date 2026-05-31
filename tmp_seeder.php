<?php
$user = App\Models\User::where('role', 'pemohon')->first();
$perijinan = App\Models\Perijinan::first();

if ($user && $perijinan) {
    for($i = 1; $i <= 15; $i++) {
        App\Models\DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'no_registrasi' => 'DUMMY-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'current_step' => 0
        ]);
    }
    echo "15 Dummy data created.\n";
} else {
    echo "No user or perijinan found.\n";
}