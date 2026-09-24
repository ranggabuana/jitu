<?php

namespace Tests\Feature;

use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedNomorSuratPerijinanTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_kode_perijinan_shares_sequential_letter_numbers_on_submission()
    {
        // 1. Create a Pemohon user
        $user = User::factory()->create([
            'username' => 'pemohon_test',
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        // 2. Create Permit A and Permit B with the same kode_perijinan
        $permitA = Perijinan::create([
            'nama_perijinan' => 'Izin Reklame Besar',
            'kode_perijinan' => 'PP-001',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum A',
            'persyaratan' => 'Persyaratan A',
            'prosedur' => 'Prosedur A',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        $permitB = Perijinan::create([
            'nama_perijinan' => 'Izin Reklame Berjalan',
            'kode_perijinan' => 'PP-001',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum B',
            'persyaratan' => 'Persyaratan B',
            'prosedur' => 'Prosedur B',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        // Verify initial numbers
        $this->assertEquals(1, $permitA->getSharedNextNomorIzin());
        $this->assertEquals(1, $permitB->getSharedNextNomorIzin());

        // 3. User submits application for Permit A
        $response1 = $this->actingAs($user)
            ->withSession(['pengajuan_num1' => 2, 'pengajuan_num2' => 3])
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $permitA->id,
                'captcha' => 5,
                'pernyataan' => 1,
            ]);

        $response1->assertRedirect();
        
        $app1 = DataPerijinan::where('perijinan_id', $permitA->id)->first();
        $this->assertNotNull($app1);
        $this->assertEquals(1, $app1->no_izin);
        $this->assertEquals(1, $app1->no_rekom);

        // After Application 1 is submitted, Permit A and Permit B must both have next_nomor = 2
        $permitA->refresh();
        $permitB->refresh();
        $this->assertEquals(2, $permitA->next_nomor_izin);
        $this->assertEquals(2, $permitB->next_nomor_izin);
        $this->assertEquals(2, $permitA->next_nomor_rekom);
        $this->assertEquals(2, $permitB->next_nomor_rekom);

        // 4. User submits application for Permit B (which shares the same kode_perijinan)
        $response2 = $this->actingAs($user)
            ->withSession(['pengajuan_num1' => 4, 'pengajuan_num2' => 1])
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $permitB->id,
                'captcha' => 5,
                'pernyataan' => 1,
            ]);

        $response2->assertRedirect();

        $app2 = DataPerijinan::where('perijinan_id', $permitB->id)->first();
        $this->assertNotNull($app2);
        // Permit B must receive sequence number 2!
        $this->assertEquals(2, $app2->no_izin);
        $this->assertEquals(2, $app2->no_rekom);

        // After Application 2 is submitted, both must now increment to 3
        $permitA->refresh();
        $permitB->refresh();
        $this->assertEquals(3, $permitA->next_nomor_izin);
        $this->assertEquals(3, $permitB->next_nomor_izin);
        $this->assertEquals(3, $permitA->next_nomor_rekom);
        $this->assertEquals(3, $permitB->next_nomor_rekom);

        // 5. User submits another application for Permit A again
        $response3 = $this->actingAs($user)
            ->withSession(['pengajuan_num1' => 1, 'pengajuan_num2' => 1])
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $permitA->id,
                'captcha' => 2,
                'pernyataan' => 1,
            ]);

        $response3->assertRedirect();

        $app3 = DataPerijinan::where('perijinan_id', $permitA->id)->latest('id')->first();
        $this->assertNotNull($app3);
        // This application must receive sequence number 3!
        $this->assertEquals(3, $app3->no_izin);
        $this->assertEquals(3, $app3->no_rekom);

        $permitA->refresh();
        $permitB->refresh();
        $this->assertEquals(4, $permitA->next_nomor_izin);
        $this->assertEquals(4, $permitB->next_nomor_izin);
    }

    public function test_different_kode_perijinan_have_independent_sequences()
    {
        $user = User::factory()->create([
            'username' => 'pemohon_test2',
            'role' => 'pemohon',
            'status' => 'aktif',
        ]);

        $permitX = Perijinan::create([
            'nama_perijinan' => 'Izin Klinik',
            'kode_perijinan' => 'KLINIK-01',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum X',
            'persyaratan' => 'Persyaratan X',
            'prosedur' => 'Prosedur X',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        $permitY = Perijinan::create([
            'nama_perijinan' => 'Izin Apotek',
            'kode_perijinan' => 'APOTEK-01',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum Y',
            'persyaratan' => 'Persyaratan Y',
            'prosedur' => 'Prosedur Y',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        // Submit for Permit X
        $this->actingAs($user)
            ->withSession(['pengajuan_num1' => 1, 'pengajuan_num2' => 1])
            ->post(route('pemohon.pengajuan.store'), [
                'perijinan_id' => $permitX->id,
                'captcha' => 2,
                'pernyataan' => 1,
            ]);

        $permitX->refresh();
        $permitY->refresh();

        // Permit X incremented to 2, Permit Y stayed at 1
        $this->assertEquals(2, $permitX->next_nomor_izin);
        $this->assertEquals(1, $permitY->next_nomor_izin);
    }

    public function test_admin_updating_nomor_urut_syncs_to_same_kode_perijinan()
    {
        $admin = User::factory()->create([
            'username' => 'admin_test',
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $permitA = Perijinan::create([
            'nama_perijinan' => 'Izin Reklame A',
            'kode_perijinan' => 'PP-SYNC',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        $permitB = Perijinan::create([
            'nama_perijinan' => 'Izin Reklame B',
            'kode_perijinan' => 'PP-SYNC',
            'jenis_perijinan' => 'umum',
            'dasar_hukum' => 'Dasar Hukum',
            'persyaratan' => 'Persyaratan',
            'prosedur' => 'Prosedur',
            'next_nomor_rekom' => 1,
            'next_nomor_izin' => 1,
        ]);

        // Admin updates Permit A template with next_nomor_izin = 50 and next_nomor_rekom = 75
        $this->actingAs($admin)
            ->put(route('perijinan.templates.update', $permitA->id), [
                'next_nomor_izin' => 50,
                'next_nomor_rekom' => 75,
            ]);

        $permitA->refresh();
        $permitB->refresh();

        $this->assertEquals(50, $permitA->next_nomor_izin);
        $this->assertEquals(50, $permitB->next_nomor_izin);
        $this->assertEquals(75, $permitA->next_nomor_rekom);
        $this->assertEquals(75, $permitB->next_nomor_rekom);
    }
}
