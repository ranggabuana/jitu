<?php

namespace Tests\Feature;

use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\User;
use App\Services\DocumentGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TableImageRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_field_resolves_dynamic_image_variables_to_img_tags()
    {
        Storage::fake('public');

        // 1. Create User
        $user = User::create([
            'name' => 'Pemohon Test',
            'username' => 'pemohon_test',
            'email' => 'pemohon@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        // 2. Create Perijinan
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Test Izin Gambar',
            'kode_perijinan' => 'IMG_TEST',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // 3. Create 'gambar' field linked dynamically
        $gambarField = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'global',
            'type' => 'gambar',
            'name' => 'foto_dokumentasi',
            'label' => 'Foto Dokumentasi',
            'status' => 'aktif',
        ]);

        // 4. Create 'table' field containing dynamic variable ${foto_dokumentasi}
        $tableField = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'rekom',
            'type' => 'table',
            'name' => 'tabel_rekom',
            'label' => 'Tabel Rekom',
            'status' => 'aktif',
            'options' => [
                'table_data' => [
                    'rows' => [
                        [
                            ['is_input' => false, 'content' => 'No'],
                            ['is_input' => false, 'content' => 'Keterangan'],
                            ['is_input' => false, 'content' => 'Foto'],
                        ],
                        [
                            ['is_input' => true, 'input_type' => 'text', 'input_name' => 'cell_1_0'],
                            ['is_input' => true, 'input_type' => 'text', 'input_name' => 'cell_1_1'],
                            ['is_input' => true, 'input_type' => 'text', 'input_name' => 'cell_1_2', 'dynamic_var' => '${foto_dokumentasi}'],
                        ]
                    ]
                ]
            ]
        ]);

        // 5. Setup mock image file in public path
        $testImagePath = secure_upload_path('uploads/perijinan/1/test_img.jpg');
        File::ensureDirectoryExists(dirname($testImagePath));
        
        // Copy real test image if exists, or use fake
        if (File::exists(base_path('gambar.jpeg'))) {
            File::copy(base_path('gambar.jpeg'), $testImagePath);
        } else {
            $uploaded = UploadedFile::fake()->image('test_img.jpg', 100, 100);
            File::copy($uploaded->getRealPath(), $testImagePath);
        }

        // 6. Create DataPerijinan
        $application = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'form_files' => [
                $gambarField->id => ['uploads/perijinan/1/test_img.jpg']
            ],
            'rekom_data' => [
                'tabel_rekom' => [
                    'cell_1_0' => '1',
                    'cell_1_1' => 'Dokumentasi Lokasi',
                    'cell_1_2' => '' // Leave blank because it's resolved dynamically
                ]
            ]
        ]);

        // Refresh with activeFormFields relationship loaded
        $application = DataPerijinan::with(['user', 'perijinan.activeFormFields'])->find($application->id);

        // 7. Invoke renderTableFieldForDocument via Reflection
        $reflector = new \ReflectionClass(DocumentGenerator::class);
        $method = $reflector->getMethod('renderTableFieldForDocument');
        $method->setAccessible(true);

        $htmlResult = $method->invoke(null, $tableField, $application->rekom_data['tabel_rekom'], $application);

        // 8. Assertions
        $this->assertStringContainsString('<table', $htmlResult);
        $this->assertStringContainsString('<img src="data:image/', $htmlResult);
        $this->assertStringContainsString('max-width: 100%', $htmlResult);

        // Cleanup
        if (File::exists($testImagePath)) {
            File::delete($testImagePath);
        }
    }
}
