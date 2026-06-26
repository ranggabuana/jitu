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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ValidatorRequiredFieldsTest extends TestCase
{
    use RefreshDatabase;

    private User $boUser;
    private User $verifikatorUser;
    private Perijinan $perijinan;
    private DataPerijinan $application;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create Users
        $this->boUser = User::create([
            'name' => 'BO User',
            'username' => 'bo_user',
            'email' => 'bo@test.com',
            'password' => bcrypt('password'),
            'role' => 'bo',
            'status' => 'aktif',
        ]);

        $this->verifikatorUser = User::create([
            'name' => 'Verifikator User',
            'username' => 'verifikator_user',
            'email' => 'verifikator@test.com',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
            'status' => 'aktif',
        ]);

        // Create Perijinan
        $this->perijinan = Perijinan::create([
            'nama_perijinan' => 'Test Izin',
            'kode_perijinan' => 'TEST_IZIN',
            'is_multi_opd' => false,
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
            'has_bo_form' => true,
        ]);

        // Create BO Fields
        PerijinanFormField::create([
            'perijinan_id' => $this->perijinan->id,
            'form_type' => 'bo',
            'type' => 'text',
            'name' => 'nomor_rekom_bo',
            'label' => 'Nomor Rekomendasi BO',
            'is_required' => true,
            'status' => 'aktif',
        ]);

        PerijinanFormField::create([
            'perijinan_id' => $this->perijinan->id,
            'form_type' => 'bo',
            'type' => 'file',
            'name' => 'dokumen_pendukung_bo',
            'label' => 'Dokumen Pendukung BO',
            'is_required' => true,
            'status' => 'aktif',
        ]);

        // Create Izin Fields
        PerijinanFormField::create([
            'perijinan_id' => $this->perijinan->id,
            'form_type' => 'izin',
            'type' => 'text',
            'name' => 'catatan_izin',
            'label' => 'Catatan Izin',
            'is_required' => true,
            'status' => 'aktif',
        ]);

        PerijinanFormField::create([
            'perijinan_id' => $this->perijinan->id,
            'form_type' => 'izin',
            'type' => 'file',
            'name' => 'sertifikat_izin',
            'label' => 'Sertifikat Izin',
            'is_required' => true,
            'status' => 'aktif',
        ]);

        // Create DataPerijinan
        $this->application = DataPerijinan::create([
            'user_id' => $this->boUser->id,
            'perijinan_id' => $this->perijinan->id,
            'status' => 'in_progress',
            'current_step' => 1,
        ]);

        // Create validation flows for BO and Verifikator
        $flowBo = PerijinanValidationFlow::create([
            'perijinan_id' => $this->perijinan->id,
            'role' => 'bo',
            'role_label' => 'Back Office',
            'order' => 1,
            'assigned_user_id' => $this->boUser->id,
            'status' => 'aktif',
        ]);

        $flowVerifikator = PerijinanValidationFlow::create([
            'perijinan_id' => $this->perijinan->id,
            'role' => 'verifikator',
            'role_label' => 'Verifikator',
            'order' => 2,
            'assigned_user_id' => $this->verifikatorUser->id,
            'status' => 'aktif',
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $this->application->id,
            'validation_flow_id' => $flowBo->id,
            'status' => 'pending',
            'order' => 1,
        ]);

        DataPerijinanValidasi::create([
            'data_perijinan_id' => $this->application->id,
            'validation_flow_id' => $flowVerifikator->id,
            'status' => 'pending',
            'order' => 2,
        ]);
    }

    public function test_save_bo_data_fails_when_required_fields_are_empty()
    {
        // 1. Submit empty fields
        $response = $this->actingAs($this->boUser)
            ->put(route('data-perijinan.bo-data.save', $this->application->id), [
                'nomor_rekom_bo' => '',
                'dokumen_pendukung_bo' => null,
            ]);

        $response->assertSessionHasErrors(['nomor_rekom_bo', 'dokumen_pendukung_bo']);
    }

    public function test_save_bo_data_succeeds_when_required_fields_are_filled()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500);

        // 2. Submit with valid inputs
        $response = $this->actingAs($this->boUser)
            ->put(route('data-perijinan.bo-data.save', $this->application->id), [
                'nomor_rekom_bo' => '123/BO/2026',
                'dokumen_pendukung_bo' => $file,
            ]);

        $response->assertRedirect();
        
        $this->application->refresh();
        $this->assertEquals('123/BO/2026', $this->application->bo_data['nomor_rekom_bo']);
        $this->assertNotNull($this->application->bo_data['dokumen_pendukung_bo']);
    }

    public function test_save_bo_data_does_not_require_file_if_already_uploaded()
    {
        // Pre-save an existing file
        $this->application->update([
            'bo_data' => [
                'nomor_rekom_bo' => '123/BO/2026',
                'dokumen_pendukung_bo' => 'uploads/perijinan/1/bo_file.pdf'
            ]
        ]);

        // Submit without the file (should pass because the file is already uploaded)
        $response = $this->actingAs($this->boUser)
            ->put(route('data-perijinan.bo-data.save', $this->application->id), [
                'nomor_rekom_bo' => '456/BO/2026',
                'dokumen_pendukung_bo' => null,
            ]);

        $response->assertRedirect();
        
        $this->application->refresh();
        $this->assertEquals('456/BO/2026', $this->application->bo_data['nomor_rekom_bo']);
        $this->assertEquals('uploads/perijinan/1/bo_file.pdf', $this->application->bo_data['dokumen_pendukung_bo']);
    }

    public function test_save_izin_data_fails_when_required_fields_are_empty()
    {
        // Advance current step to 2 (Verifikator step)
        $this->application->update(['current_step' => 2]);

        // 1. Submit empty fields
        $response = $this->actingAs($this->verifikatorUser)
            ->put(route('data-perijinan.izin-data.save', $this->application->id), [
                'catatan_izin' => '',
                'sertifikat_izin' => null,
                'masa_aktif' => '',
            ]);

        $response->assertSessionHasErrors(['catatan_izin', 'sertifikat_izin', 'masa_aktif']);
    }

    public function test_save_izin_data_succeeds_when_required_fields_are_filled()
    {
        // Advance current step to 2 (Verifikator step)
        $this->application->update(['current_step' => 2]);

        $file = UploadedFile::fake()->create('certificate.pdf', 500);

        // 2. Submit with valid inputs
        $response = $this->actingAs($this->verifikatorUser)
            ->put(route('data-perijinan.izin-data.save', $this->application->id), [
                'catatan_izin' => 'Izin disetujui',
                'sertifikat_izin' => $file,
                'masa_aktif' => '2026-12-31',
            ]);

        $response->assertRedirect();
        
        $this->application->refresh();
        $this->assertEquals('Izin disetujui', $this->application->izin_data['catatan_izin']);
        $this->assertNotNull($this->application->izin_data['sertifikat_izin']);
        $this->assertEquals('2026-12-31', $this->application->masa_aktif->format('Y-m-d'));
    }

    public function test_save_izin_data_does_not_require_file_if_already_uploaded()
    {
        // Advance current step to 2 (Verifikator step)
        $this->application->update([
            'current_step' => 2,
            'izin_data' => [
                'catatan_izin' => 'Izin disetujui',
                'sertifikat_izin' => 'uploads/perijinan/1/izin_file.pdf'
            ],
            'masa_aktif' => '2026-12-31',
        ]);

        // Submit without the file (should pass because the file is already uploaded)
        $response = $this->actingAs($this->verifikatorUser)
            ->put(route('data-perijinan.izin-data.save', $this->application->id), [
                'catatan_izin' => 'Izin disetujui baru',
                'sertifikat_izin' => null,
                'masa_aktif' => '2027-12-31',
            ]);

        $response->assertRedirect();
        
        $this->application->refresh();
        $this->assertEquals('Izin disetujui baru', $this->application->izin_data['catatan_izin']);
        $this->assertEquals('uploads/perijinan/1/izin_file.pdf', $this->application->izin_data['sertifikat_izin']);
        $this->assertEquals('2027-12-31', $this->application->masa_aktif->format('Y-m-d'));
    }
}
