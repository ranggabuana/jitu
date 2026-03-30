<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opdList = [
            'Dinas Kesehatan',
            'Dinas Pendidikan',
            'Dinas Pekerjaan Umum',
            'Dinas Perhubungan',
            'Dinas Perdagangan',
            'Dinas Pertanian',
            'Dinas Perikanan',
            'Dinas Kehutanan',
            'Dinas Energi dan Sumber Daya Mineral',
            'Dinas Pariwisata',
            'Dinas Sosial',
            'Dinas Tenaga Kerja',
            'Dinas Koperasi dan Usaha Kecil Menengah',
            'Dinas Lingkungan Hidup',
            'Dinas Pemuda dan Olahraga',
            'Dinas Kebudayaan',
            'Dinas Pangan',
            'Dinas Perumahan dan Kawasan Permukiman',
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
            'Dinas Komunikasi dan Informatika',
            'Badan Perencanaan Pembangunan Daerah',
            'Badan Keuangan Daerah',
            'Badan Pendapatan Daerah',
            'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia',
            'Badan Penanggulangan Bencana Daerah',
            'Badan Kesatuan Bangsa dan Politik',
            'Badan Penelitian dan Pengembangan Daerah',
            'Badan Pengelola Keuangan dan Aset Daerah',
            'Kantor Satuan Polisi Pamong Praja',
            'Kantor Arsip dan Perpustakaan Daerah',
            'Inspektorat Daerah',
            'Sekretariat Dewan Perwakilan Rakyat Daerah',
        ];

        foreach ($opdList as $namaOpd) {
            Opd::firstOrCreate(['nama_opd' => $namaOpd]);
        }

        // Tambah data random menggunakan factory jika diperlukan
        // Opd::factory()->count(20)->create();
    }
}
