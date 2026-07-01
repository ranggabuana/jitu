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
            'file_rekom' => 'uploads/rekom_old.pdf',
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

        // Create dummy files for old TTE files and drafts
        $oldIzinPath = public_path($completedApp->file_izin_tte);
        $oldRekomPath = public_path($completedApp->file_rekom_tte);
        $oldIzinDraftPath = public_path($completedApp->file_izin);
        $oldRekomDraftPath = public_path($completedApp->file_rekom);
        if (!file_exists(dirname($oldIzinPath))) {
            mkdir(dirname($oldIzinPath), 0755, true);
        }
        file_put_contents($oldIzinPath, 'dummy old izin tte');
        file_put_contents($oldRekomPath, 'dummy old rekom tte');
        file_put_contents($oldIzinDraftPath, 'dummy old izin draft');
        file_put_contents($oldRekomDraftPath, 'dummy old rekom draft');

        // 5. Test POST Store Page as correction
        $storeResponse = $this->actingAs($pemohon)
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $perijinan->id,
                'pembetulan_from' => $completedApp->id,
                'alasan_pembetulan' => 'Alasan pembetulan tes',
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
        $this->assertEquals(1, DataPerijinan::count());

        $newApp = DataPerijinan::findOrFail($completedApp->id);
        $this->assertTrue($newApp->is_pembetulan);
        $this->assertEquals('Alasan pembetulan tes', $newApp->alasan_pembetulan);
        $this->assertEquals('Dr. Budi Santoso (Dibetulkan)', $newApp->form_data[$fieldNama->id]);
        $this->assertEquals('submitted', $newApp->status);
        $this->assertEquals($completedApp->id, $newApp->pembetulan_dari_id);

        // Verify that BO data is updated to match the corrected global value
        $this->assertNotNull($newApp->bo_data);
        $this->assertEquals('Dr. Budi Santoso (Dibetulkan)', $newApp->bo_data['nama_pemilik']);

        // Verify TTE files: active file_izin_tte is reset, but old one is backed up
        $this->assertNull($newApp->file_izin_tte);
        
        // Verify backup columns are populated
        $this->assertNotNull($newApp->file_izin_tte_pembetulan_old);
        $this->assertNotNull($newApp->file_rekom_tte_pembetulan_old);
        $this->assertNotNull($newApp->file_izin_pembetulan_old);
        $this->assertNotNull($newApp->file_rekom_pembetulan_old);
        
        $this->assertFileExists(public_path($newApp->file_izin_tte_pembetulan_old));
        $this->assertFileExists(public_path($newApp->file_rekom_tte_pembetulan_old));
        $this->assertFileExists(public_path($newApp->file_izin_pembetulan_old));
        $this->assertFileExists(public_path($newApp->file_rekom_pembetulan_old));

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

        // Clean up old files
        @unlink($oldIzinPath);
        @unlink($oldRekomPath);
        @unlink($oldIzinDraftPath);
        @unlink($oldRekomDraftPath);
        @unlink(public_path($newApp->file_izin_tte_pembetulan_old));
        @unlink(public_path($newApp->file_rekom_tte_pembetulan_old));
        @unlink(public_path($newApp->file_izin_pembetulan_old));
        @unlink(public_path($newApp->file_rekom_pembetulan_old));

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

    public function test_detail_page_displays_correct_badges_for_different_application_types()
    {
        // Create Admin/Validator user who has access
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $pemohon = User::create([
            'name' => 'Pemohon Test',
            'username' => 'pemohon_test',
            'email' => 'pemohon@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan',
            'kode_perijinan' => 'KLINIK_TES',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // 1. New application (Pengajuan Izin)
        $newApp = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'submitted',
            'current_step' => 1,
            'no_registrasi' => 'REG-NEW',
        ]);

        $responseNew = $this->actingAs($admin)
            ->get(route('data-perijinan.show', $newApp->id));
        $responseNew->assertStatus(200);
        $responseNew->assertSee('Pengajuan Izin');
        $responseNew->assertDontSee('Pembetulan Izin');
        $responseNew->assertDontSee('Perpanjang Izin');

        // 2. Renewal application (Perpanjang Izin)
        $renewApp = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'submitted',
            'current_step' => 1,
            'no_registrasi' => 'REG-RENEW',
            'perpanjang_dari_id' => $newApp->id,
        ]);

        $responseRenew = $this->actingAs($admin)
            ->get(route('data-perijinan.show', $renewApp->id));
        $responseRenew->assertStatus(200);
        $responseRenew->assertSee('Perpanjang Izin');
        $responseRenew->assertDontSee('Pembetulan Izin');
        $responseRenew->assertDontSee('Pengajuan Izin');

        // 3. Correction application (Pembetulan Izin)
        $correctApp = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'submitted',
            'current_step' => 1,
            'no_registrasi' => 'REG-CORRECT',
            'is_pembetulan' => true,
            'pembetulan_dari_id' => $newApp->id,
        ]);

        $responseCorrect = $this->actingAs($admin)
            ->get(route('data-perijinan.show', $correctApp->id));
        $responseCorrect->assertStatus(200);
        $responseCorrect->assertSee('Pembetulan Izin');
        $responseCorrect->assertDontSee('Perpanjang Izin');
        $responseCorrect->assertDontSee('Pengajuan Izin');

        // --- Test List Pages ---

        // 4. Test Dalam Proses List Page (where status = submitted)
        $responseDalamProses = $this->actingAs($admin)
            ->get(route('data-perijinan.dalam-proses'));
        $responseDalamProses->assertStatus(200);
        $responseDalamProses->assertSee('Pengajuan Izin');
        $responseDalamProses->assertSee('Perpanjang Izin');
        $responseDalamProses->assertSee('Pembetulan Izin');

        // 5. Test Perlu Perbaikan List Page (status = perbaikan)
        $newApp->status = 'perbaikan';
        $newApp->save();

        $responsePerluPerbaikan = $this->actingAs($admin)
            ->get(route('data-perijinan.perlu-perbaikan'));
        $responsePerluPerbaikan->assertStatus(200);
        $responsePerluPerbaikan->assertSee('Pengajuan Izin');
        $responsePerluPerbaikan->assertDontSee('Perpanjang Izin');
        $responsePerluPerbaikan->assertDontSee('Pembetulan Izin');

        // 6. Test Selesai List Page (status = approved)
        $renewApp->status = 'approved';
        $renewApp->save();

        $responseSelesai = $this->actingAs($admin)
            ->get(route('data-perijinan.selesai'));
        $responseSelesai->assertStatus(200);
        $responseSelesai->assertSee('Perpanjang Izin');
        $responseSelesai->assertDontSee('Pengajuan Izin');
        $responseSelesai->assertDontSee('Pembetulan Izin');

        // 7. Test Ditolak List Page (status = rejected)
        $correctApp->status = 'rejected';
        $correctApp->save();

        $responseDitolak = $this->actingAs($admin)
            ->get(route('data-perijinan.ditolak'));
        $responseDitolak->assertStatus(200);
        $responseDitolak->assertSee('Pembetulan Izin');
        $responseDitolak->assertDontSee('Pengajuan Izin');
        $responseDitolak->assertDontSee('Perpanjang Izin');
    }

    public function test_bo_can_upload_docx_template_for_pembetulan_and_convert_to_pdf()
    {
        // 1. Setup User and Application
        $boUser = User::create([
            'name' => 'BO User',
            'username' => 'bo_user',
            'email' => 'bo@test.com',
            'password' => bcrypt('password'),
            'role' => 'bo',
            'status' => 'aktif',
        ]);

        $pemohon = User::create([
            'name' => 'Pemohon Test',
            'username' => 'pemohon_test',
            'email' => 'pemohon@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan',
            'kode_perijinan' => 'KLINIK_TES',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        $application = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
            'no_registrasi' => 'REG-PEMBETULAN-TEST',
            'is_pembetulan' => true,
        ]);

        // Mock a validation record for current step
        $flow = PerijinanValidationFlow::create([
            'perijinan_id' => $perijinan->id,
            'role' => 'bo',
            'role_label' => 'Back Office',
            'order' => 1,
            'status' => 'aktif',
            'assigned_user_id' => $boUser->id,
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $application->id,
            'validation_flow_id' => $flow->id,
            'user_id' => $boUser->id,
            'status' => 'pending',
            'order' => 1,
        ]);

        // Create a dummy template docx file content
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Template Izin Pembetulan ${NAMA_PEMOHON} ${QRCODE}');
        $tempDocxFile = tempnam(sys_get_temp_dir(), 'test_docx') . '.docx';
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempDocxFile);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempDocxFile,
            'template.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        // 2. Submit the DOCX template upload
        $response = $this->actingAs($boUser)
            ->put(route('data-perijinan.bo-data.save', $application->id), [
                'file_izin_pembetulan' => $uploadedFile,
            ]);

        // Clean up temp template docx file
        @unlink($tempDocxFile);

        $response->assertRedirect();
        
        // 3. Assert database paths
        $application = $application->fresh();
        $this->assertNotNull($application->file_izin_pembetulan);
        // Path should be a PDF
        $this->assertStringEndsWith('.pdf', $application->file_izin_pembetulan);
        $this->assertFileExists(public_path($application->file_izin_pembetulan));
        
        // And the corresponding docx template should also exist on disk
        $docxTemplatePath = str_replace('.pdf', '_template.docx', $application->file_izin_pembetulan);
        $this->assertFileExists(public_path($docxTemplatePath));

        // Clean up created files
        @unlink(public_path($application->file_izin_pembetulan));
        @unlink(public_path($docxTemplatePath));
    }

    public function test_verifier_can_refresh_pembetulan_pdf()
    {
        // 1. Setup User, Verifikator, and Application
        $verifikator = User::create([
            'name' => 'Verifier User',
            'username' => 'verifier_user',
            'email' => 'verifier@test.com',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
            'status' => 'aktif',
        ]);

        $pemohon = User::create([
            'name' => 'Pemohon Test',
            'username' => 'pemohon_test2',
            'email' => 'pemohon2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan 2',
            'kode_perijinan' => 'KLINIK_TES2',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create application with dummy PDF and dummy DOCX template
        $pdfPath = 'uploads/perijinan/' . $perijinan->id . '/izin_pembetulan_test.pdf';
        $docxPath = 'uploads/perijinan/' . $perijinan->id . '/izin_pembetulan_test_template.docx';
        
        // Make sure destination directory exists
        @mkdir(public_path('uploads/perijinan/' . $perijinan->id), 0755, true);

        // Generate dynamic docx template
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Template Izin Pembetulan ${NAMA_PEMOHON} ${QRCODE} ${NOMOR_SURAT}');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save(public_path($docxPath));
        
        // Write dummy PDF
        file_put_contents(public_path($pdfPath), '%PDF-1.4 ... dummy PDF ...');

        $application = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
            'no_registrasi' => 'REG-REFRESH-TEST',
            'is_pembetulan' => true,
            'file_izin_pembetulan' => $pdfPath,
        ]);

        // 2. Call the refresh route
        $response = $this->actingAs($verifikator)
            ->post(route('data-perijinan.pembetulan.refresh', $application->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Clean up created files
        @unlink(public_path($pdfPath));
        @unlink(public_path($docxPath));
        if ($application->fresh()->file_izin_pembetulan) {
            @unlink(public_path($application->fresh()->file_izin_pembetulan));
            @unlink(public_path(str_replace('.pdf', '_template.docx', $application->fresh()->file_izin_pembetulan)));
        }
    }

    public function test_kadin_can_sign_pembetulan_permit_and_keep_draft_red()
    {
        // 1. Setup User Kadin & Pemohon
        $kadin = User::create([
            'name' => 'Kadin User',
            'username' => 'kadin_user',
            'email' => 'kadin@test.com',
            'password' => bcrypt('password'),
            'role' => 'kadin',
            'nip' => '1234567890123456',
            'status' => 'aktif',
        ]);

        $pemohon = User::create([
            'name' => 'Pemohon Test 3',
            'username' => 'pemohon_test3',
            'email' => 'pemohon3@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan 3',
            'kode_perijinan' => 'KLINIK_TES3',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create application with dummy PDF and dummy DOCX template
        $pdfPath = 'uploads/perijinan/' . $perijinan->id . '/izin_pembetulan_sign_test.pdf';
        $docxPath = 'uploads/perijinan/' . $perijinan->id . '/izin_pembetulan_sign_test_template.docx';
        
        // Make sure destination directory exists
        @mkdir(public_path('uploads/perijinan/' . $perijinan->id), 0755, true);

        // Generate dynamic docx template
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Template Izin Pembetulan ${NAMA_PEMOHON} ${QRCODE} ${NOMOR_SURAT}');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save(public_path($docxPath));
        
        // Write dummy PDF
        file_put_contents(public_path($pdfPath), '%PDF-1.4 ... dummy PDF ...');

        $application = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 4, // kadin turn
            'no_registrasi' => 'REG-SIGN-TEST',
            'is_pembetulan' => true,
            'file_izin_pembetulan' => $pdfPath,
        ]);

        // Mock EsignService response
        \Illuminate\Support\Facades\Http::fake([
            '*/sign/pdf' => \Illuminate\Support\Facades\Http::response([
                'file' => [base64_encode('%PDF-1.4 ... signed PDF ...')]
            ], 200, ['Content-Type' => 'application/json']),
        ]);

        // 2. Call the TTE signing route
        $response = $this->actingAs($kadin)
            ->post(route('data-perijinan.apply-tte', $application->id), [
                'doc_type' => 'izin',
                'passphrase' => 'secret123',
            ]);

        $response->assertJson(['success' => true]);

        // Refresh application from database
        $application = $application->fresh();

        // 3. Assert database paths and file statuses
        $this->assertNotNull($application->file_izin_tte);
        $this->assertFileExists(public_path($application->file_izin_tte));

        // The draft file path must remain unchanged
        $this->assertEquals($pdfPath, $application->file_izin_pembetulan);
        $this->assertFileExists(public_path($pdfPath));

        // The generated official (non-signed/intermediate) PDF file should also exist
        $officialPdfPath = str_replace('.pdf', '_official.pdf', $pdfPath);
        $this->assertFileExists(public_path($officialPdfPath));

        // Clean up created files
        @unlink(public_path($pdfPath));
        @unlink(public_path($docxPath));
        @unlink(public_path($officialPdfPath));
        if ($application->file_izin_tte) {
            @unlink(public_path($application->file_izin_tte));
        }
    }

    public function test_qr_code_scan_result_for_pembetulan_status_checks()
    {
        $pemohon = User::create([
            'name' => 'Pemohon Test 4',
            'username' => 'pemohon_test4',
            'email' => 'pemohon4@test.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $perijinan = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik Kesehatan 4',
            'kode_perijinan' => 'KLINIK_TES4',
            'is_multi_opd' => false,
            'dasar_hukum' => 'UUD',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
        ]);

        // Create application
        $application = DataPerijinan::create([
            'user_id' => $pemohon->id,
            'perijinan_id' => $perijinan->id,
            'status' => 'in_progress',
            'current_step' => 2,
            'no_registrasi' => 'REG-SCAN-CHECK-99',
            'is_pembetulan' => true,
            'file_izin_pembetulan' => 'uploads/perijinan/1/izin_draft.pdf',
            'file_rekom_tte' => 'uploads/perijinan/1/rekom_signed.pdf', // recommendation is signed
        ]);

        // 1. Scan the BO draft of pembetulan
        $response1 = $this->get(route('front.perizinan.scan', [
            'no_registrasi' => $application->no_registrasi,
            'type' => 'izin',
            'is_draft' => 1,
            'is_pembetulan' => 1
        ]));
        $response1->assertStatus(200);
        $response1->assertSee('Draft Dokumen');

        // 2. Kadin signs the permit (simulate setting file_izin_tte)
        $application->update([
            'file_izin_tte' => 'uploads/perijinan/1/izin_signed.pdf',
        ]);

        // 3. Scan the NEW pembetulan permit (should be Resmi TTE)
        $response2 = $this->get(route('front.perizinan.scan', [
            'no_registrasi' => $application->no_registrasi,
            'type' => 'izin',
            'is_draft' => 0,
            'is_pembetulan' => 1
        ]));
        $response2->assertStatus(200);
        $response2->assertSee('Resmi (TTE)');

        // 4. Scan the OLD permit letter (no is_pembetulan parameter, should be Tidak Berlaku)
        $response3 = $this->get(route('front.perizinan.scan', [
            'no_registrasi' => $application->no_registrasi,
            'type' => 'izin',
            'is_draft' => 0
        ]));
        $response3->assertStatus(200);
        $response3->assertSee('sudah tidak berlaku lagi karena telah diterbitkan surat izin baru hasil pembetulan', false);

        // 5. Scan the recommendation (should still be Resmi TTE)
        $response4 = $this->get(route('front.perizinan.scan', [
            'no_registrasi' => $application->no_registrasi,
            'type' => 'rekom',
            'is_draft' => 0
        ]));
        $response4->assertStatus(200);
        $response4->assertSee('Resmi (TTE)');
    }
}

