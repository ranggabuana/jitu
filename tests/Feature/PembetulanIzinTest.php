<?php

namespace Tests\Feature;

use App\Models\DataPerijinan;
use App\Models\DataPerijinanValidasi;
use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\PerijinanValidationFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembetulanIzinTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembetulan_izin_prefills_form_data_and_filters_validation_flow()
    {
        // 1. Create Pemohon User
        $pemohon = User::create([
            'name' => 'Pemohon Test',
            'username' => 'pemohon_test',
            'email' => 'pemohon@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        // 2. Create Perijinan
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan',
            'kode_perijinan' => 'KLINIK_TES',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create global form fields
        $fieldNama = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'global',
            'type' => 'text',
            'name' => 'nama_pemilik',
            'label' => 'Nama Pemilik Klinik',
            'status' => 'aktif',
            'order' => 1,
        ]);

        $fieldAlamat = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'global',
            'type' => 'text',
            'name' => 'alamat_klinik',
            'label' => 'Alamat Klinik',
            'status' => 'aktif',
            'order' => 2,
        ]);

        // Create 6 validation steps: FO, BO, Operator OPD, Kepala OPD, Verifikator, Kadin
        $roles = ['fo', 'bo', 'operator_opd', 'kepala_opd', 'verifikator', 'kadin'];
        foreach ($roles as $idx => $role) {
            PerijinanValidationFlow::create([
                'perijinan_id' => $perijinan->id,
                'role' => $role,
                'role_label' => ucfirst($role),
                'order' => $idx + 1,
                'status' => 'aktif',
            ]);
        }

        // 3. Create completed application (status = approved)
        $completedApp = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'current_step' => 6,
            'form_data' => [
                $fieldNama->id => 'Dr. Budi Santoso',
                $fieldAlamat->id => 'Jl. Pemuda No. 45',
            ],
            'file_izin' => 'uploads/izin_old.pdf',
            'file_izin_tte' => 'uploads/izin_old_tte.pdf',
        ]);

        // 4. Test GET Create Page with pembetulan_from pre-filling
        $createResponse = $this->actingAs($pemohon)
            ->get(route('pemohon.pengajuan.create', [
                'perijinanId' => $perijinan->id,
                'pembetulan_from' => $completedApp->id
            ]));

        $createResponse->assertStatus(200);
        $createResponse->assertSee('Dr. Budi Santoso');
        $createResponse->assertSee('Jl. Pemuda No. 45');

        // Setup CAPTCHA answer
        session([
            'pengajuan_num1' => 5,
            'pengajuan_num2' => 5,
        ]);

        // 5. Test POST Store Page as correction
        $storeResponse = $this->actingAs($pemohon)
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $perijinan->id,
                'pembetulan_from' => $completedApp->id,
                'form_fields' => [
                    $fieldNama->id => 'Dr. Budi Santoso (Dibetulkan)',
                    $fieldAlamat->id => 'Jl. Pemuda No. 45',
                ],
                'captcha' => 10,
                'pernyataan' => 1,
            ]);

        $storeResponse->assertRedirect();

        // 6. Assert new application is created
        $newApp = DataPerijinan::where('pembetulan_dari_id', $completedApp->id)->first();
        $this->assertNotNull($newApp);
        $this->assertEquals('Dr. Budi Santoso (Dibetulkan)', $newApp->form_data[$fieldNama->id]);
        $this->assertEquals('submitted', $newApp->status);

        // 7. Verify TTE files are reset (null) on the new application
        $this->assertNull($newApp->file_izin_tte);
        $this->assertNull($newApp->file_rekom_tte);

        // 8. Assert validation flow: only FO, BO, Verifikator, Kadin. Operator/Kepala OPD are skipped!
        $validasiRecords = DataPerijinanValidasi::where('data_perijinan_id', $newApp->id)
            ->with('validationFlow')
            ->get();

        // Should have 4 records instead of 6
        $this->assertCount(4, $validasiRecords);

        // Ensure roles are strictly fo, bo, verifikator, kadin (no operator_opd, kepala_opd)
        $validRoles = [];
        foreach ($validasiRecords as $rec) {
            $validRoles[] = $rec->validationFlow->role;
        }

        $this->assertEquals(['fo', 'bo', 'verifikator', 'kadin'], $validRoles);
    }
}
