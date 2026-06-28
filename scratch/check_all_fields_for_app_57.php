<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestApp = \App\Models\DataPerijinan::findOrFail(57);
$array = $latestApp->toArray();
foreach ($array as $key => $val) {
    if (is_array($val) || is_object($val)) {
        echo "$key: " . json_encode($val) . "\n";
    } else {
        echo "$key: $val\n";
    }
}
