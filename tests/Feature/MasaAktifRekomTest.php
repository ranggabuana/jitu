<?php

namespace Tests\Feature;

use App\Models\DataPerijinan;
use App\Models\DataPerijinanValidasi;
use App\Models\Opd;
use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\PerijinanValidationFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasaAktifRekomTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_rekom_data_for_single_opd()
    {
        // 1. Create OPD
        $opd = Opd::create([
            'nama_opd' => 'Dinas Kesehatan',
            'kode_opd' => 'DINKES',
        ]);

        // 2. Create User as Admin
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // 3. Create Perijinan (Single OPD)
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Apotek',
            'kode_perijinan' => 'APOTEK',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create some rekom fields
        $field = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'rekom',
            'type' => 'text',
            'name' => 'catatan_teknis',
            'label' => 'Catatan Teknis',
            'status' => 'aktif',
        ]);

        // 4. Create DataPerijinan
        $application = DataPerijinan::create([
            'user_id' => $admin->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
        ]);

        // 5. Submit recommendation data with masa_aktif_rekom
        $response = $this->actingAs($admin)
            ->put(route('data-perijinan.rekom-data.save', $application->id), [
                'catatan_teknis' => 'Rekomendasi layak',
                'masa_aktif_rekom' => '2026-12-31',
            ]);

        $response->assertRedirect();
        
        $application->refresh();
        $this->assertNotNull($application->rekom_data);
        $this->assertEquals('2026-12-31', $application->rekom_data['masa_aktif_rekom']);
        $this->assertEquals('Rekomendasi layak', $application->rekom_data['catatan_teknis']);

        // Test display on scan result for single OPD
        $scanResponse = $this->get('/perizinan/scan/' . $application->no_registrasi . '?type=rekom');
        $scanResponse->assertStatus(200);
        $scanResponse->assertSee('Masa Aktif Rekomendasi s/d');
        $scanResponse->assertSee('31/12/2026');
    }

    public function test_save_rekom_data_for_multi_opd()
    {
        // 1. Create OPDs
        $opd1 = Opd::create([
            'nama_opd' => 'Dinas Kesehatan',
            'kode_opd' => 'DINKES',
        ]);
        $opd2 = Opd::create([
            'nama_opd' => 'Dinas Lingkungan Hidup',
            'kode_opd' => 'DLH',
        ]);

        // 2. Create Operator Users for OPDs
        $userOpd1 = User::create([
            'name' => 'Operator Dinkes',
            'username' => 'op_dinkes',
            'email' => 'dinkes@test.com',
            'password' => bcrypt('password'),
            'role' => 'operator_opd',
            'opd_id' => $opd1->id,
            'status' => 'aktif',
        ]);

        $userOpd2 = User::create([
            'name' => 'Operator DLH',
            'username' => 'op_dlh',
            'email' => 'dlh@test.com',
            'password' => bcrypt('password'),
            'role' => 'operator_opd',
            'opd_id' => $opd2->id,
            'status' => 'aktif',
        ]);

        // 3. Create Perijinan (Multi OPD)
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin RS',
            'kode_perijinan' => 'RS',
            'is_multi_opd' => true,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create some rekom fields linked to specific OPDs
        PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'rekom',
            'type' => 'text',
            'name' => 'dinkes_notes',
            'label' => 'Catatan Dinkes',
            'opd_id' => $opd1->id,
            'status' => 'aktif',
        ]);

        PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'rekom',
            'type' => 'text',
            'name' => 'dlh_notes',
            'label' => 'Catatan DLH',
            'opd_id' => $opd2->id,
            'status' => 'aktif',
        ]);

        // 4. Create DataPerijinan
        $application = DataPerijinan::create([
            'user_id' => $userOpd1->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
        ]);

        // Add validation flow records to satisfy strict steps validation if acting as operator
        $flow1 = PerijinanValidationFlow::create([
            'perijinan_id' => $perijinan->id,
            'role' => 'operator_opd',
            'role_label' => 'Operator Dinkes',
            'order' => 1,
            'assigned_user_id' => $userOpd1->id,
            'status' => 'aktif',
        ]);

        $flow2 = PerijinanValidationFlow::create([
            'perijinan_id' => $perijinan->id,
            'role' => 'operator_opd',
            'role_label' => 'Operator DLH',
            'order' => 2,
            'assigned_user_id' => $userOpd2->id,
            'status' => 'aktif',
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $application->id,
            'validation_flow_id' => $flow1->id,
            'status' => 'pending',
            'order' => 1,
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $application->id,
            'validation_flow_id' => $flow2->id,
            'status' => 'pending',
            'order' => 2,
        ]);

        // 5. Submit recommendation data for OPD 1 (Dinkes)
        $response1 = $this->actingAs($userOpd1)
            ->put(route('data-perijinan.rekom-data.save', $application->id), [
                'dinkes_notes' => 'Aman dari penyakit menular',
                'masa_aktif_rekom' => '2026-10-31',
            ]);

        $response1->assertRedirect();
        
        // 6. Submit recommendation data for OPD 2 (DLH)
        $response2 = $this->actingAs($userOpd2)
            ->put(route('data-perijinan.rekom-data.save', $application->id), [
                'dlh_notes' => 'Aman AMDAL',
                'masa_aktif_rekom' => '2027-05-15',
            ]);

        $response2->assertRedirect();

        $application->refresh();

        // 7. Verify both records are saved separately inside rekom_data_multi
        $this->assertNotNull($application->rekom_data_multi);
        $this->assertArrayHasKey($opd1->id, $application->rekom_data_multi);
        $this->assertArrayHasKey($opd2->id, $application->rekom_data_multi);

        $this->assertEquals('2026-10-31', $application->rekom_data_multi[$opd1->id]['masa_aktif_rekom']);
        $this->assertEquals('Aman dari penyakit menular', $application->rekom_data_multi[$opd1->id]['dinkes_notes']);

        $this->assertEquals('2027-05-15', $application->rekom_data_multi[$opd2->id]['masa_aktif_rekom']);
        $this->assertEquals('Aman AMDAL', $application->rekom_data_multi[$opd2->id]['dlh_notes']);

        // Test display on scan result for OPD 1
        $scanResponse1 = $this->get("/perizinan/scan/{$application->no_registrasi}?type=rekom&opd_id={$opd1->id}");
        $scanResponse1->assertStatus(200);
        $scanResponse1->assertSee('Masa Aktif Rekomendasi s/d');
        $scanResponse1->assertSee('31/10/2026');

        // Test display on scan result for OPD 2
        $scanResponse2 = $this->get("/perizinan/scan/{$application->no_registrasi}?type=rekom&opd_id={$opd2->id}");
        $scanResponse2->assertStatus(200);
        $scanResponse2->assertSee('Masa Aktif Rekomendasi s/d');
        $scanResponse2->assertSee('15/05/2027');
    }
}
