<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataPerijinan;
use App\Services\DocumentGenerator;

$application = DataPerijinan::with(['perijinan.activeFormFields', 'user'])->findOrFail(57);

echo "Generating documents for Application ID 57...\n";
$docs = DocumentGenerator::generateDocuments($application);
print_r($docs);

$pdfPath = public_path($docs['file_rekom']);
$docxPath = str_replace('.pdf', '.docx', $pdfPath);

if (file_exists($docxPath)) {
    echo "Generated DOCX exists at: " . $docxPath . "\n";
    $zip = new ZipArchive();
    if ($zip->open($docxPath) === TRUE) {
        echo "Files inside generated DOCX:\n";
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (str_contains($filename, 'media/')) {
                echo "Media file: " . $filename . " (" . strlen($zip->getFromName($filename)) . " bytes)\n";
            }
        }
        $zip->close();
    }
} else {
    echo "Generated DOCX does NOT exist at " . $docxPath . "\n";
}
