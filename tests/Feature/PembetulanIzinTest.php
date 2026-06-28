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

        $fieldFile = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'global',
            'type' => 'file',
            'name' => 'surat_keterangan',
            'label' => 'Surat Keterangan',
            'is_required' => true,
            'status' => 'aktif',
            'order' => 3,
        ]);

        $fieldNamaBo = PerijinanFormField::create([
            'perijinan_id' => $perijinan->id,
            'form_type' => 'bo',
            'type' => 'text',
            'name' => 'nama_pemilik',
            'label' => 'Nama Pemilik Klinik',
            'status' => 'aktif',
            'order' => 4,
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
            'no_registrasi' => 'REG-999',
            'no_izin' => 123,
            'no_izin_kode' => 'KLN-TEST',
            'no_rekom' => 456,
            'no_rekom_kode' => 'REK-TEST',
            'form_data' => [
                $fieldNama->id => 'Dr. Budi Santoso',
                $fieldAlamat->id => 'Jl. Pemuda No. 45',
            ],
            'form_files' => [
                $fieldFile->id => ['uploads/perijinan/1/test_sk.pdf'],
            ],
            'bo_data' => [
                'nama_pemilik' => 'Dr. Budi Santoso',
            ],
            'file_izin' => 'uploads/izin_old.pdf',
            'file_izin_tte' => 'uploads/izin_old_tte.pdf',
            'file_rekom_tte' => 'uploads/rekom_old_tte.pdf',
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
        $createResponse->assertSee('test_sk.pdf');

        // Setup CAPTCHA answer
        session([
            'pengajuan_num1' => 5,
            'pengajuan_num2' => 5,
        ]);

        // Create dummy file for copy check
        $dummyPath = public_path('uploads/perijinan/1/test_sk.pdf');
        if (!file_exists(dirname($dummyPath))) {
            mkdir(dirname($dummyPath), 0755, true);
        }
        file_put_contents($dummyPath, 'dummy content');

        // 5. Test POST Store Page as correction
        $storeResponse = $this->actingAs($pemohon)
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $perijinan->id,
                'pembetulan_from' => $completedApp->id,
                'form_fields' => [
                    $fieldNama->id => 'Dr. Budi Santoso (Dibetulkan)',
                    $fieldAlamat->id => 'Jl. Pemuda No. 45',
                ],
                'old_files' => [
                    $fieldFile->id => ['uploads/perijinan/1/test_sk.pdf'],
                ],
                'captcha' => 10,
                'pernyataan' => 1,
            ]);

        // Clean up dummy file
        @unlink($dummyPath);

        $storeResponse->assertRedirect();

        // 6. Assert same application is updated (no new row created)
        $newApp = DataPerijinan::findOrFail($completedApp->id);
        $this->assertEquals(1, DataPerijinan::count());
        $this->assertTrue($newApp->is_pembetulan);
        $this->assertEquals('Dr. Budi Santoso (Dibetulkan)', $newApp->form_data[$fieldNama->id]);
        $this->assertEquals('submitted', $newApp->status);
        $this->assertEquals($completedApp->id, $newApp->pembetulan_dari_id);

        // Verify that BO data is updated to match the corrected global value
        $this->assertNotNull($newApp->bo_data);
        $this->assertEquals('Dr. Budi Santoso (Dibetulkan)', $newApp->bo_data['nama_pemilik']);

        // Verify TTE files: file_izin_tte is reset, but file_rekom_tte is kept/preserved!
        $this->assertNull($newApp->file_izin_tte);
        $this->assertEquals('uploads/rekom_old_tte.pdf', $newApp->file_rekom_tte);

        // Verify registration number and permit/rekom numbers are preserved
        $this->assertEquals('REG-999', $newApp->no_registrasi);
        $this->assertEquals(123, $newApp->no_izin);
        $this->assertEquals('KLN-TEST', $newApp->no_izin_kode);
        $this->assertEquals(456, $newApp->no_rekom);
        $this->assertEquals('REK-TEST', $newApp->no_rekom_kode);

        // Verify old file is copied to new application
        $this->assertNotNull($newApp->form_files);
        $this->assertNotEmpty($newApp->form_files[$fieldFile->id]);
        $this->assertStringContainsString('test_sk', $newApp->form_files[$fieldFile->id][0]);
        // Clean up newly copied file
        $copiedPath = public_path($newApp->form_files[$fieldFile->id][0]);
        @unlink($copiedPath);

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

        // 9. Update validation records to approved to simulate validation completion
        DataPerijinanValidasi::where('data_perijinan_id', $newApp->id)->update(['status' => 'approved']);

        // Update status to 'diperbaiki' (representing completed correction)
        $newApp->status = 'diperbaiki';
        $newApp->file_izin = 'uploads/izin_corrected.pdf';
        $newApp->save();

        // Assert progress percentage is exactly 100%
        $this->assertEquals(100.0, $newApp->fresh()->progress_percentage);

        // 10. Check tracking detail page displays SKM invitation before SKM is filled
        $trackingResponse = $this->actingAs($pemohon)
            ->get(route('pemohon.tracking.detail', $newApp->id));

        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee('Isi Survei Sekarang & Unduh Izin', false);

        // 11. Create a DataSkm question to satisfy foreign key constraint
        $dataSkm = \App\Models\DataSkm::create([
            'pertanyaan' => 'Bagaimana pelayanan kami?',
            'bobot_max' => 4,
            'urutan' => 1,
            'status' => 'aktif',
            'opsi_1' => 'Sangat Buruk',
            'opsi_2' => 'Buruk',
            'opsi_3' => 'Baik',
            'opsi_4' => 'Sangat Baik',
            'user_id' => $pemohon->id,
        ]);

        // 12. Simulate SKM being filled
        \App\Models\HasilSkm::create([
            'data_skm_id' => $dataSkm->id,
            'data_perijinan_id' => $newApp->id,
            'responden_nama' => 'Pemohon Test',
            'responden_email' => 'pemohon@test.com',
            'jawaban' => '{"1": 4}',
        ]);

        // 13. Check tracking detail page displays download button after SKM is filled
        $trackingResponse2 = $this->actingAs($pemohon)
            ->get(route('pemohon.tracking.detail', $newApp->id));

        $trackingResponse2->assertStatus(200);
        $trackingResponse2->assertSee('Unduh Dokumen Izin');

        // 14. Check that we can initiate a second correction from 'diperbaiki' status
        $createResponse2 = $this->actingAs($pemohon)
            ->get(route('pemohon.pengajuan.create', [
                'perijinanId' => $perijinan->id,
                'pembetulan_from' => $newApp->id
            ]));

        $createResponse2->assertStatus(200);
        $createResponse2->assertSee('Dr. Budi Santoso (Dibetulkan)');

        // 15. Verify that completing validation deletes SKM records so pemohon has to fill a new one
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Set back to in_progress to trigger status transition
        $newApp->status = 'in_progress';
        $newApp->save();

        // Verify HasilSkm exists
        $this->assertEquals(1, \App\Models\HasilSkm::where('data_perijinan_id', $newApp->id)->count());

        // Submit approval
        $this->actingAs($admin)
            ->patch(route('data-perijinan.update-status', $newApp->id), [
                'status' => 'approved',
                'catatan' => 'Selesai pembetulan',
            ]);

        // Assert status changed to diperbaiki and HasilSkm was deleted
        $this->assertEquals('diperbaiki', $newApp->fresh()->status);
        $this->assertEquals(0, \App\Models\HasilSkm::where('data_perijinan_id', $newApp->id)->count());

        // Check tracking detail page displays SKM questionnaire questions (meaning skmQuestions is successfully loaded)
        $trackingResponse3 = $this->actingAs($pemohon)
            ->get(route('pemohon.tracking.detail', $newApp->id));

        $trackingResponse3->assertStatus(200);
        $trackingResponse3->assertSee('Bagaimana pelayanan kami?');
    }
}
