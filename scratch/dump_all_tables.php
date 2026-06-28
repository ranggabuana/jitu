<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fields = \App\Models\PerijinanFormField::where('type', 'table')->get();
foreach ($fields as $field) {
    echo "ID: " . $field->id . "\n";
    echo "Perijinan ID: " . $field->perijinan_id . " (" . ($field->perijinan->nama_perijinan ?? 'N/A') . ")\n";
    echo "Name: " . $field->name . "\n";
    echo "Label: " . $field->label . "\n";
    echo "Type: " . $field->type . "\n";
    echo "Form Type: " . $field->form_type . "\n";
    echo "Options: " . json_encode($field->options, JSON_PRETTY_PRINT) . "\n";
    echo "----------------------------------------\n";
}
