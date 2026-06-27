<?php

namespace Tests\Feature;

use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeactivatePermitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_deactivate_and_activate_permit()
    {
        // 1. Create User as Admin
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // 2. Create Perijinan
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Apotek',
            'kode_perijinan' => 'APOTEK',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // 3. Create DataPerijinan (Selesai/Approved status)
        $application = DataPerijinan::create([
            'user_id' => $admin->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'current_step' => 1,
            'approved_at' => now(),
            'masa_aktif' => now()->addYear(),
        ]);

        // 4. Check that finished list shows the permit and its active period
        $response = $this->actingAs($admin)->get(route('data-perijinan.selesai'));
        $response->assertStatus(200);
        $response->assertSee($application->no_registrasi);
        $response->assertSee($application->masa_aktif->format('d M Y'));
        $response->assertSee('Nonaktifkan');

        // 5. Deactivate the permit
        $deactivateResponse = $this->actingAs($admin)->post(route('data-perijinan.deactivate', $application->id));
        $deactivateResponse->assertRedirect();
        
        $application->refresh();
        $this->assertTrue($application->is_deactivated);

        // 6. Scan QR code (default/izin) and verify it shows the deactivation warning for Izin
        $scanResponse = $this->get(route('front.perizinan.scan', $application->no_registrasi));
        $scanResponse->assertStatus(200);
        $scanResponse->assertSee('Surat Izin Dinonaktifkan oleh Admin DPMPTSP');
        $scanResponse->assertSee('PERINGATAN: SURAT IZIN DINONAKTIFKAN');
        $scanResponse->assertSee('Surat izin ini telah dinonaktifkan oleh');
        $scanResponse->assertSee('Admin DPMPTSP');

        // Test scan QR code (rekom) and verify it shows the deactivation warning for Rekomendasi
        $scanRekomResponse = $this->get(route('front.perizinan.scan', $application->no_registrasi) . '?type=rekom');
        $scanRekomResponse->assertStatus(200);
        $scanRekomResponse->assertSee('Surat Rekomendasi Dinonaktifkan oleh Admin DPMPTSP');
        $scanRekomResponse->assertSee('PERINGATAN: SURAT REKOMENDASI DINONAKTIFKAN');
        $scanRekomResponse->assertSee('Surat rekomendasi ini telah dinonaktifkan oleh');
        $scanRekomResponse->assertSee('Admin DPMPTSP');

        // 7. Reactivate the permit
        $activateResponse = $this->actingAs($admin)->post(route('data-perijinan.activate', $application->id));
        $activateResponse->assertRedirect();
        
        $application->refresh();
        $this->assertFalse($application->is_deactivated);

        // 8. Scan QR code and verify warning is gone
        $scanResponse2 = $this->get(route('front.perizinan.scan', $application->no_registrasi));
        $scanResponse2->assertStatus(200);
        $scanResponse2->assertDontSee('Surat Izin Dinonaktifkan oleh Admin DPMPTSP');
        $scanResponse2->assertDontSee('PERINGATAN: SURAT IZIN DINONAKTIFKAN');
    }

    public function test_deactivate_multi_opd_permit()
    {
        // 1. Create User as Admin
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_multi',
            'email' => 'admin_multi@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Create OPD
        $opd = \App\Models\Opd::create([
            'nama_opd' => 'Dinas Kesehatan',
            'kode_opd' => 'DINKES',
        ]);

        // Create Operator User
        $operator = User::create([
            'name' => 'Budi Utomo',
            'username' => 'opd_multi_test',
            'email' => 'opd_multi@test.com',
            'password' => bcrypt('password'),
            'role' => 'operator_opd',
            'opd_id' => $opd->id,
            'status' => 'aktif',
        ]);

        // 2. Create Perijinan (is_multi_opd = true)
        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Rumah Sakit',
            'kode_perijinan' => 'IRS',
            'is_multi_opd' => true,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // 3. Create DataPerijinan
        $application = DataPerijinan::create([
            'user_id' => $admin->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'current_step' => 1,
            'is_deactivated' => true, // Start as deactivated to test scan
            'approved_at' => now(),
            'masa_aktif' => now()->addYear(),
        ]);

        // Create validation flow to link OPD with the validation records for scan mapping
        $flow = \App\Models\PerijinanValidationFlow::create([
            'perijinan_id' => $perijinan->id,
            'role' => 'operator_opd',
            'role_label' => 'Operator Dinkes',
            'order' => 1,
            'assigned_user_id' => $operator->id,
            'status' => 'aktif',
        ]);

        \App\Models\DataPerijinanValidasi::create([
            'data_perijinan_id' => $application->id,
            'validation_flow_id' => $flow->id,
            'status' => 'approved',
            'order' => 1,
        ]);

        // 4. Scan recommendation for multi OPD (with type=rekom and opd_id)
        $scanRekomResponse = $this->get(route('front.perizinan.scan', $application->no_registrasi) . '?type=rekom&opd_id=' . $opd->id);
        $scanRekomResponse->assertStatus(200);
        $scanRekomResponse->assertSee('Surat Rekomendasi Dinonaktifkan oleh Admin DPMPTSP');
        $scanRekomResponse->assertSee('PERINGATAN: SURAT REKOMENDASI DINONAKTIFKAN');
        $scanRekomResponse->assertSee('Dinas Kesehatan'); // Should display the OPD name in the scan result

        // 5. Verify pemohon tracking detail hides validator name but shows role and OPD name
        $trackingResponse = $this->actingAs($admin)->get(route('pemohon.tracking.detail', $application->id));
        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee('Operator OPD');
        $trackingResponse->assertSee('Dinas Kesehatan');
        $trackingResponse->assertDontSee($operator->name); // Should not see "Budi Utomo"

        // 6. Verify public tracking API hides validator name but shows role and OPD name
        $publicTrackingResponse = $this->post(route('front.perizinan.track'), [
            'no_registrasi' => $application->no_registrasi
        ]);
        $publicTrackingResponse->assertStatus(200);
        $publicTrackingResponse->assertJsonFragment([
            'role_label' => 'Tim Teknis OPD',
            'opd_name' => 'Dinas Kesehatan'
        ]);
        $publicTrackingResponse->assertJsonMissing([
            'name' => $operator->name
        ]);
    }
}
