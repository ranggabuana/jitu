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

        // 2. Create User as Operator OPD
        $operatorOpd = User::create([
            'name' => 'Operator Dinkes',
            'username' => 'opd_test',
            'email' => 'opd@test.com',
            'password' => bcrypt('password'),
            'role' => 'operator_opd',
            'opd_id' => $opd->id,
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
            'user_id' => $operatorOpd->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
        ]);

        $flow = PerijinanValidationFlow::create([
            'perijinan_id' => $perijinan->id,
            'role' => 'operator_opd',
            'role_label' => 'Operator Dinkes',
            'order' => 1,
            'assigned_user_id' => $operatorOpd->id,
            'status' => 'aktif',
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $application->id,
            'validation_flow_id' => $flow->id,
            'status' => 'pending',
            'order' => 1,
        ]);

        // 5. Submit recommendation data with masa_aktif_rekom
        $response = $this->actingAs($operatorOpd)
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

    public function test_expired_rekom_and_izin_documents()
    {
        $opd = Opd::create([
            'nama_opd' => 'Dinas Kesehatan',
            'kode_opd' => 'DINKES',
        ]);

        $user = User::create([
            'name' => 'Pemohon User',
            'username' => 'pemohon_test',
            'email' => 'pemohon@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik',
            'kode_perijinan' => 'KLINIK',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // 1. Test Expired Recommendation (rekom)
        $appRekomExpired = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'rekom_data' => [
                'masa_aktif_rekom' => '2020-01-01', // Past date
            ],
        ]);

        $scanRekomExpired = $this->get("/perizinan/scan/{$appRekomExpired->no_registrasi}?type=rekom");
        $scanRekomExpired->assertStatus(200);
        $scanRekomExpired->assertSee('PERINGATAN: DOKUMEN TIDAK AKTIF');
        $scanRekomExpired->assertSee('01/01/2020');

        // 2. Test Expired Permit (izin)
        $appIzinExpired = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'masa_aktif' => '2021-06-15', // Past date
        ]);

        $scanIzinExpired = $this->get("/perizinan/scan/{$appIzinExpired->no_registrasi}?type=izin");
        $scanIzinExpired->assertStatus(200);
        $scanIzinExpired->assertSee('PERINGATAN: DOKUMEN TIDAK AKTIF');
        $scanIzinExpired->assertSee('15/06/2021');

        // 3. Test Inactive Status Label & Color Accessors on Expired Permit
        $this->assertEquals('Habis Masa', $appIzinExpired->status_label);
        $this->assertEquals('bg-red-100 text-red-800', $appIzinExpired->status_color);
    }

    public function test_renewal_creates_new_application_and_marks_old_as_diperpanjang()
    {
        $opd = Opd::create([
            'nama_opd' => 'Dinas Kesehatan',
            'kode_opd' => 'DINKES',
        ]);

        $user = User::create([
            'name' => 'Pemohon User',
            'username' => 'pemohon_test2',
            'email' => 'pemohon2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Apotek',
            'kode_perijinan' => 'APOTEK',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create the old approved application
        $oldApp = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'masa_aktif' => '2026-06-01',
        ]);

        // Submit new application that renews from the old one
        session(['pengajuan_num1' => 5, 'pengajuan_num2' => 5]);

        $response = $this->actingAs($user)
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $perijinan->id,
                'captcha' => 10, // 5 + 5
                'pernyataan' => 1,
                'renew_from' => $oldApp->id,
                'form_fields' => [],
            ]);

        $response->assertRedirect();

        // Verify the old application is updated to 'diperpanjang'
        $oldApp->refresh();
        $this->assertEquals('diperpanjang', $oldApp->status);
        $this->assertEquals('Diperpanjang', $oldApp->status_label);
        $this->assertEquals('bg-indigo-100 text-indigo-800 border border-indigo-200', $oldApp->status_color);

        // Verify the new application is created with correct parent and root references
        $newApp = DataPerijinan::where('user_id', $user->id)->where('status', 'submitted')->first();
        $this->assertNotNull($newApp);
        $this->assertEquals($oldApp->id, $newApp->perpanjang_dari_id);
        $this->assertEquals($oldApp->id, $newApp->root_perpanjang_id);
        $this->assertNotEquals($oldApp->no_registrasi, $newApp->no_registrasi);

        // If we renew again from the new app (once it is approved)
        $newApp->update(['status' => 'approved', 'masa_aktif' => '2027-06-01']);

        session(['pengajuan_num1' => 3, 'pengajuan_num2' => 4]);
        $response2 = $this->actingAs($user)
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $perijinan->id,
                'captcha' => 7, // 3 + 4
                'pernyataan' => 1,
                'renew_from' => $newApp->id,
                'form_fields' => [],
            ]);

        $response2->assertRedirect();

        $newApp->refresh();
        $this->assertEquals('diperpanjang', $newApp->status);

        $newestApp = DataPerijinan::where('user_id', $user->id)->where('status', 'submitted')->first();
        $this->assertNotNull($newestApp);
        $this->assertEquals($newApp->id, $newestApp->perpanjang_dari_id);
        // Root perpanjang ID should be the original oldApp ID!
        $this->assertEquals($oldApp->id, $newestApp->root_perpanjang_id);
    }

    public function test_today_is_considered_active()
    {
        $user = User::create([
            'name' => 'Pemohon User',
            'username' => 'pemohon_test3',
            'email' => 'pemohon3@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Test Aktif',
            'kode_perijinan' => 'TEST_AKTIF',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create application with active date set to today
        $app = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'masa_aktif' => now()->toDateString(), // Today's date
        ]);

        // Asserts status label is NOT 'Habis Masa'
        $this->assertNotEquals('Habis Masa', $app->status_label);
        $this->assertEquals('Disetujui & Selesai', $app->status_label);
        $this->assertEquals('bg-green-100 text-green-800', $app->status_color);

        // Scan page check should not see "PERINGATAN: DOKUMEN TIDAK AKTIF"
        $scan = $this->get("/perizinan/scan/{$app->no_registrasi}?type=izin");
        $scan->assertStatus(200);
        $scan->assertDontSee('PERINGATAN: DOKUMEN TIDAK AKTIF');
        $scan->assertSee('Dokumen Sah & Berlaku', false);
    }

    public function test_renewed_permit_shows_deactivated_warning_on_scan()
    {
        $user = User::create([
            'name' => 'Pemohon User',
            'username' => 'pemohon_test4',
            'email' => 'pemohon4@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Test Diperpanjang',
            'kode_perijinan' => 'TEST_DI_PERPANJANG',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create application with status set to diperpanjang
        $app = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'diperpanjang',
            'masa_aktif' => now()->subYear()->toDateString(), // Already past
        ]);

        // Scan page check should see warning: Dokumen Tidak Berlaku (Telah Diperpanjang)
        $scan = $this->get("/perizinan/scan/{$app->no_registrasi}?type=izin");
        $scan->assertStatus(200);
        $scan->assertSee('Dokumen Tidak Berlaku (Telah Diperpanjang)', false);
        $scan->assertSee('tidak berlaku lagi karena izin telah diperpanjang', false);
    }

    public function test_renewal_history_is_displayed_on_detail_pages()
    {
        $user = User::create([
            'name' => 'Pemohon User History',
            'username' => 'pemohon_history',
            'email' => 'pemohon_history@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_history',
            'email' => 'admin_history@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Test History',
            'kode_perijinan' => 'TEST_HISTORY',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Original application A
        $appA = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'diperpanjang',
        ]);

        // First renewal B
        $appB = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'diperpanjang',
            'perpanjang_dari_id' => $appA->id,
            'root_perpanjang_id' => $appA->id,
        ]);

        // Second renewal C
        $appC = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
            'perpanjang_dari_id' => $appB->id,
            'root_perpanjang_id' => $appA->id,
        ]);

        // 1. Check pemohon tracking detail page for App C
        $response1 = $this->actingAs($user)
            ->get(route('pemohon.tracking.detail', $appC->id));
        $response1->assertStatus(200);
        $response1->assertSee('Histori Perpanjangan Izin');
        $response1->assertSee($appA->no_registrasi);
        $response1->assertSee($appB->no_registrasi);
        $response1->assertSee($appC->no_registrasi);

        // 2. Check admin detail page for App C
        $response2 = $this->actingAs($admin)
            ->get(route('data-perijinan.show', $appC->id));
        $response2->assertStatus(200);
        $response2->assertSee('Histori Perpanjangan Izin');
        $response2->assertSee($appA->no_registrasi);
        $response2->assertSee($appB->no_registrasi);
        $response2->assertSee($appC->no_registrasi);
    }

    public function test_renewal_shows_pengajuan_perpanjangan_label()
    {
        $user = User::create([
            'name' => 'Pemohon User',
            'username' => 'pemohon_label_test',
            'email' => 'pemohon_label@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Test Label',
            'kode_perijinan' => 'TEST_LABEL',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Original application A
        $appA = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'approved',
        ]);

        // Submitted renewal B
        $appB = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'submitted',
            'perpanjang_dari_id' => $appA->id,
            'root_perpanjang_id' => $appA->id,
        ]);

        // Assert that $appB's status_label is 'Pengajuan Perpanjangan'
        $this->assertEquals('Pengajuan Perpanjangan', $appB->status_label);
        $this->assertStringContainsString('bg-purple-100', $appB->status_color);

        // Fetch tracking JSON via LandingPageController
        $response = $this->post(route('front.perizinan.track'), [
            'no_registrasi' => $appB->no_registrasi,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.status_label', 'Pengajuan Perpanjangan');
    }

    public function test_pemohon_tracking_page_supports_filtering_searching_sorting_and_per_page()
    {
        $user = User::create([
            'name' => 'Pemohon Tracking',
            'username' => 'pemohon_track',
            'email' => 'pemohon_track@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan1 = Perijinan::create([
            'nama_perijinan' => 'Izin Kelayakan Lingkungan',
            'kode_perijinan' => 'IZIN_KL',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        $perijinan2 = Perijinan::create([
            'nama_perijinan' => 'Izin Usaha Mikro',
            'kode_perijinan' => 'IZIN_IUM',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create 2 applications
        $app1 = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan1->id,
            'status' => 'submitted',
        ]);

        $app2 = DataPerijinan::create([
            'user_id' => $user->id,
            'perijinan_id' => $perijinan2->id,
            'status' => 'in_progress',
        ]);

        // 1. Check index page loads
        $response = $this->actingAs($user)
            ->get(route('pemohon.tracking'));
        $response->assertStatus(200);
        $response->assertSee($app1->no_registrasi);
        $response->assertSee($app2->no_registrasi);

        // 2. Test search parameter
        $responseSearch = $this->actingAs($user)
            ->get(route('pemohon.tracking', ['search' => 'Mikro']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee($app2->no_registrasi);
        $responseSearch->assertDontSee($app1->no_registrasi);

        // 3. Test sorting parameter
        $responseSort = $this->actingAs($user)
            ->get(route('pemohon.tracking', ['sort' => 'no_registrasi', 'order' => 'asc']));
        $responseSort->assertStatus(200);

        // 4. Test per_page parameter
        $responsePerPage = $this->actingAs($user)
            ->get(route('pemohon.tracking', ['per_page' => 1]));
        $responsePerPage->assertStatus(200);
    }
}
