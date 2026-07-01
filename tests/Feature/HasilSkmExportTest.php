<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DataSkm;
use App\Models\HasilSkm;
use App\Models\Perijinan;
use App\Models\DataPerijinan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasilSkmExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_skm_export_contains_custom_labels(): void
    {
        // 1. Create an admin user to access the route
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'username' => 'admintest',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Create perizinan and application relationship dependencies
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Reklame',
            'kode_perijinan' => 'REKLAME_TES',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
            'status' => 'aktif',
            'sektor' => 'pekerjaan_umum',
        ]);

        $pemohon = User::create([
            'name' => 'Pemohon Test',
            'email' => 'pemohon@test.com',
            'username' => 'pemohontest',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'active',
        ]);

        $application = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'current_step' => 6,
            'no_registrasi' => 'REG-SKM-EXPORT-123',
        ]);

        // 3. Create a DataSkm question with custom labels
        $dataSkm = DataSkm::create([
            'pertanyaan' => 'Bagaimana keramahan petugas?',
            'bobot_max' => 4,
            'urutan' => 1,
            'status' => 'aktif',
            'opsi_1' => 'Sangat Kasar',
            'opsi_2' => 'Kasar',
            'opsi_3' => 'Ramah Sekali',
            'opsi_4' => 'Sempurna Ramah',
            'user_id' => $admin->id,
        ]);

        // 4. Create HasilSkm response pointing to the question and application
        HasilSkm::create([
            'data_skm_id' => $dataSkm->id,
            'data_perijinan_id' => $application->id,
            'responden_nama' => 'Budi Sudarsono',
            'responden_email' => 'budi@gmail.com',
            'nip' => '123456789',
            'jawaban' => '3',
            'saran' => 'Saran tes',
            'user_id' => $pemohon->id,
        ]);

        // 5. Send GET request to the export route as admin
        $response = $this->actingAs($admin)
            ->get(route('skm.hasil.export'));

        // 6. Assert response is download containing correct Excel content
        $response->assertStatus(200);
        $this->assertStringStartsWith('attachment; filename="hasil_skm_', $response->headers->get('Content-Disposition'));
        
        // Assert that the custom option label 'Ramah Sekali' is printed in the Excel content
        $this->assertStringContainsString('Ramah Sekali', $response->getContent());
        $this->assertStringNotContainsString('Cukup Baik', $response->getContent());
    }
}
