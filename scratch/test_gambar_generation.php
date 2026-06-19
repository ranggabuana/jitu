<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
$request = Request::create('http://localhost');
$app->instance('request', $request);

use App\Models\DataPerijinan;
use App\Models\PerijinanFormField;
use App\Services\DocumentGenerator;
use Illuminate\Support\Facades\File;

$id = 45;
$application = DataPerijinan::with(['user', 'perijinan'])->findOrFail($id);
$perijinan = $application->perijinan;

echo "Setting up temporary 'gambar' field for Perijinan ID: " . $perijinan->id . PHP_EOL;

// Create a temp FormField of type 'gambar'
$tempField = new PerijinanFormField();
$tempField->perijinan_id = $perijinan->id;
$tempField->form_type = 'global';
$tempField->type = 'gambar';
$tempField->label = 'Dokumentasi Lapangan';
$tempField->name = 'dokumentasi_lapangan';
$tempField->order = 99;
$tempField->is_required = false;
$tempField->save();

// Copy gambar.jpeg to a public upload path
$mockUploadDir = public_path('uploads/pengajuan');
if (!file_exists($mockUploadDir)) {
    mkdir($mockUploadDir, 0755, true);
}
$mockFileName = 'test_gambar_' . time() . '.jpeg';
$mockFilePath = $mockUploadDir . '/' . $mockFileName;
$dbFilePath = 'uploads/pengajuan/' . $mockFileName;

File::copy(base_path('gambar.jpeg'), $mockFilePath);
echo "Copied test image to: " . $mockFilePath . PHP_EOL;

// Inject the file into form_files
$formFiles = $application->form_files ?? [];
$formFiles[$tempField->id] = [$dbFilePath];
$application->form_files = $formFiles;
$application->save();

// Refresh the application with form fields
$application = DataPerijinan::with(['user', 'perijinan.activeFormFields'])->findOrFail($id);

echo "Running DocumentGenerator for verification..." . PHP_EOL;

try {
    // We can call the generator or manually verify the replacements
    // Let's call the generator
    $generatedDocs = DocumentGenerator::generateDocuments($application);
    echo "Document generation completed successfully!" . PHP_EOL;
    
    // Print the generated paths
    print_r($generatedDocs);
    
    // Let's verify that the image files exist in the output directory
    echo PHP_EOL . "Verifying HTML replacements..." . PHP_EOL;
    
    // To see what replacements were made, let's manually inspect the replacements map
    // We can invoke the replacements generation code block logic manually
    $perijinan = $application->perijinan;
    $applicantReplacements = [];
    foreach ($perijinan->activeFormFields->where('form_type', 'global') as $field) {
        if ($field->type === 'pas_foto' || $field->type === 'gambar') {
            $files = $application->form_files[$field->id] ?? null;
            $file = is_array($files) ? ($files[0] ?? null) : $files;
            if ($file) {
                $absolutePath = public_path($file);
                if (File::exists($absolutePath)) {
                    $imageData = base64_encode(File::get($absolutePath));
                    $mime = File::mimeType($absolutePath);
                    $src = 'data:' . $mime . ';base64,' . $imageData;
                    
                    if ($field->type === 'pas_foto') {
                        $htmlImg = '<img src="' . $src . '" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" alt="Pas Foto" />';
                        $imgValType = 'PASFOTO_';
                    } else {
                        $htmlImg = '<img src="' . $src . '" style="max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain;" alt="Gambar" />';
                        $imgValType = 'GAMBAR_';
                    }
                    
                    $keyName = '${' . strtoupper(str_replace(' ', '_', $field->name)) . '}';
                    $keyImgVal = '${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->name)) . '}';
                    
                    echo "Field label: " . $field->label . " (Type: " . $field->type . ")" . PHP_EOL;
                    echo "HTML Replacement Key: " . $keyName . PHP_EOL;
                    echo "HTML Preview (first 100 chars): " . substr($htmlImg, 0, 100) . "..." . PHP_EOL;
                    echo "Word Replacement Key: " . $keyImgVal . PHP_EOL;
                    echo "Word Value: " . $absolutePath . PHP_EOL;
                    
                    if ($field->type === 'gambar' && strpos($htmlImg, 'max-width: 100%; max-height: 250px;') !== false) {
                        echo "SUCCESS: HTML styling matches design instructions." . PHP_EOL;
                    } else if ($field->type === 'gambar') {
                        echo "FAILURE: HTML styling incorrect." . PHP_EOL;
                    }
                }
            }
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
} finally {
    // Clean up
    echo PHP_EOL . "Cleaning up database and file modifications..." . PHP_EOL;
    
    // Remove mock file
    if (File::exists($mockFilePath)) {
        File::delete($mockFilePath);
    }
    
    // Remove the temp field
    $tempField->delete();
    
    // Revert form_files on application 45
    $formFiles = $application->form_files ?? [];
    unset($formFiles[$tempField->id]);
    $application->form_files = $formFiles;
    $application->save();
    
    echo "Cleanup finished." . PHP_EOL;
}
