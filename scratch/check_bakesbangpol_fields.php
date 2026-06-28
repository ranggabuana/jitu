<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fields = \App\Models\PerijinanFormField::where('perijinan_id', 25)->get();
foreach ($fields as $field) {
    echo "ID: " . $field->id . "\n";
    echo "Name: " . $field->name . "\n";
    echo "Label: " . $field->label . "\n";
    echo "Type: " . $field->type . "\n";
    echo "Form Type: " . $field->form_type . "\n";
    echo "----------------------------------------\n";
}
