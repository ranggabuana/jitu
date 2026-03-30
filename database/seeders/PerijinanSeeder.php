<?php

namespace Database\Seeders;

use App\Models\Perijinan;
use Illuminate\Database\Seeder;

class PerijinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perijinanList = [
            [
                'nama_perijinan' => 'Izin Mendirikan Bangunan (IMB)',
                'dasar_hukum' => 'Undang-Undang No. 28 Tahun 2002 tentang Bangunan Gedung',
                'persyaratan' => '1. Fotokopi KTP pemohon
2. Fotokopi sertifikat tanah atau bukti kepemilikan lahan
3. Gambar rencana bangunan
4. Surat izin dari tetangga sekitar
5. Formulir permohonan yang sudah diisi',
                'prosedur' => '1. Mengambil formulir permohonan di kantor dinas terkait
2. Mengisi formulir dan melengkapi dokumen persyaratan
3. Menyerahkan berkas permohonan ke loket pelayanan
4. Membayar biaya retribusi perijinan
5. Menunggu proses verifikasi dan survei lokasi
6. Mengambil IMB yang sudah terbit',
            ],
            [
                'nama_perijinan' => 'Izin Usaha Perdagangan (IUP)',
                'dasar_hukum' => 'Undang-Undang No. 7 Tahun 2014 tentang Perdagangan',
                'persyaratan' => '1. Fotokopi KTP pemilik usaha
2. Fotokopi NPWP
3. Surat keterangan domisili usaha
4. Foto tempat usaha
5. Daftar barang yang akan diperdagangkan',
                'prosedur' => '1. Menyiapkan dokumen persyaratan
2. Mengisi formulir permohonan online
3. Upload dokumen persyaratan
4. Verifikasi dokumen oleh petugas
5. Penerbitan IUP',
            ],
            [
                'nama_perijinan' => 'Izin Gangguan (HO)',
                'dasar_hukum' => 'Undang-Undang No. 1 Tahun 1970 tentang Keselamatan Kerja',
                'persyaratan' => '1. Fotokopi KTP pemohon
2. Fotokopi NPWP
3. Surat keterangan domisili
4. Denah lokasi usaha
5. Surat persetujuan tetangga',
                'prosedur' => '1. Mengajukan permohonan ke kelurahan
2. Mendapatkan surat persetujuan tetangga
3. Submit berkas ke dinas terkait
4. Pembayaran retribusi
5. Penerbitan izin',
            ],
            [
                'nama_perijinan' => 'Izin Usaha Industri (IUI)',
                'dasar_hukum' => 'Undang-Undang No. 3 Tahun 2014 tentang Perindustrian',
                'persyaratan' => '1. Akta pendirian perusahaan
2. NPWP perusahaan
3. Surat izin lokasi
4. Dokumen AMDAL atau UKL-UPL
5. Daftar mesin dan peralatan produksi',
                'prosedur' => '1. Registrasi online
2. Upload dokumen persyaratan
3. Verifikasi administrasi
4. Survei lokasi industri
5. Penerbitan IUI',
            ],
            [
                'nama_perijinan' => 'Izin Reklame',
                'dasar_hukum' => 'Peraturan Daerah tentang Pajak Reklame',
                'persyaratan' => '1. Fotokopi KTP pemohon
2. Gambar rencana reklame
3. Surat izin pemilik lahan/tempat
4. Lokasi pemasangan reklame',
                'prosedur' => '1. Mengajukan permohonan
2. Survey lokasi pemasangan
3. Penetapan pajak reklame
4. Pembayaran pajak
5. Penerbitan izin reklame',
            ],
        ];

        foreach ($perijinanList as $perijinan) {
            Perijinan::firstOrCreate(
                ['nama_perijinan' => $perijinan['nama_perijinan']],
                $perijinan
            );
        }
    }
}
