<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user or create one if not exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => bcrypt('password'),
            ]);
        }

        $beritas = [
            [
                'judul' => 'Pelayanan Perizinan Semakin Mudah dan Cepat',
                'slug' => 'pelayanan-perizinan-semakin-mudah-dan-cepat',
                'konten' => '<p>Pemerintah terus berupaya meningkatkan kualitas pelayanan perizinan kepada masyarakat. Dengan adanya sistem online, proses perizinan kini dapat dilakukan dengan lebih mudah dan cepat.</p>
                
<h2>Keuntungan Sistem Online</h2>
<p>Sistem perizinan online memberikan banyak keuntungan bagi masyarakat, antara lain:</p>
<ul>
<li>Tidak perlu antri di loket pelayanan</li>
<li>Dapat mengajukan permohonan kapan saja dan di mana saja</li>
<li>Proses lebih transparan dan dapat dipantau secara real-time</li>
<li>Biaya lebih efisien</li>
</ul>

<h2>Cara Mengajukan Perizinan Online</h2>
<p>Masyarakat dapat mengakses layanan perizinan online melalui website resmi. Cukup registrasi akun, lengkapi persyaratan, dan ikuti panduan yang tersedia.</p>

<blockquote>Kami berkomitmen untuk terus meningkatkan kualitas pelayanan kepada masyarakat.</blockquote>

<p>Untuk informasi lebih lanjut, masyarakat dapat menghubungi call center atau mengunjungi kantor pelayanan terdekat.</p>',
                'gambar' => null,
                'status' => 'aktif',
                'user_id' => $user->id,
                'views' => 125,
            ],
            [
                'judul' => 'Sosialisasi Peraturan Perizinan Terbaru Tahun 2026',
                'slug' => 'sosialisasi-peraturan-perizinan-terbaru-tahun-2026',
                'konten' => '<p>Dalam rangka meningkatkan pemahaman masyarakat tentang peraturan perizinan terbaru, pemerintah akan mengadakan sosialisasi secara berkala di berbagai daerah.</p>

<h2>Jadwal Sosialisasi</h2>
<p>Sosialisasi akan dilaksanakan di beberapa kota besar dengan jadwal sebagai berikut:</p>
<ol>
<li><strong>Jakarta</strong> - 15 Maret 2026</li>
<li><strong>Surabaya</strong> - 22 Maret 2026</li>
<li><strong>Medan</strong> - 29 Maret 2026</li>
<li><strong>Makassar</strong> - 5 April 2026</li>
</ol>

<h2>Topik Sosialisasi</h2>
<p>Adapun topik yang akan dibahas dalam sosialisasi ini meliputi:</p>
<ul>
<li>Perubahan regulasi perizinan usaha</li>
<li>Tata cara pengajuan izin baru</li>
<li>Sistem pengawasan dan pelaporan</li>
<li>Sanksi dan penghargaan</li>
</ul>

<p>Masyarakat diimbau untuk mengikuti sosialisasi ini guna mendapatkan informasi yang akurat dan lengkap tentang peraturan perizinan terbaru.</p>

<p>Pendaftaran sosialisasi dapat dilakukan melalui website resmi atau menghubungi panitia setempat.</p>',
                'gambar' => null,
                'status' => 'aktif',
                'user_id' => $user->id,
                'views' => 89,
            ],
            [
                'judul' => 'Jam Operasional Pelayanan Perizinan Diperpanjang',
                'slug' => 'jam-operasional-pelayanan-perizinan-diperpanjang',
                'konten' => '<p>Untuk memberikan pelayanan yang lebih baik kepada masyarakat, kantor pelayanan perizinan memperpanjang jam operasional mulai bulan Maret 2026.</p>

<h2>Jam Operasional Baru</h2>
<table>
<thead>
<tr>
<th>Hari</th>
<th>Jam Buka</th>
<th>Jam Tutup</th>
</tr>
</thead>
<tbody>
<tr>
<td>Senin - Kamis</td>
<td>08:00 WIB</td>
<td>16:00 WIB</td>
</tr>
<tr>
<td>Jumat</td>
<td>08:00 WIB</td>
<td>15:00 WIB</td>
</tr>
<tr>
<td>Sabtu</td>
<td>09:00 WIB</td>
<td>13:00 WIB</td>
</tr>
</tbody>
</table>

<h2>Layanan Sabtu</h2>
<p>Dengan dibukanya layanan hari Sabtu, masyarakat yang memiliki keterbatasan waktu di hari kerja dapat memanfaatkan layanan ini untuk mengurus perizinan.</p>

<p><strong>Catatan:</strong> Layanan Sabtu hanya tersedia untuk jenis perizinan tertentu. Silakan hubungi call center untuk informasi lebih lanjut.</p>

<p>Kami berharap dengan perpanjangan jam operasional ini dapat meningkatkan kepuasan masyarakat terhadap pelayanan perizinan.</p>',
                'gambar' => null,
                'status' => 'aktif',
                'user_id' => $user->id,
                'views' => 234,
            ],
            [
                'judul' => 'Update Sistem: Pemeliharaan Berkala',
                'slug' => 'update-sistem-pemeliharaan-berkala',
                'konten' => '<p>Diberitahukan kepada seluruh pengguna layanan perizinan online, bahwa akan dilakukan pemeliharaan berkala terhadap sistem.</p>

<h2>Jadwal Pemeliharaan</h2>
<p><strong>Tanggal:</strong> Minggu, 15 Maret 2026<br>
<strong>Waktu:</strong> 00:00 - 06:00 WIB</p>

<h2>Dampak Pemeliharaan</h2>
<p>Selama proses pemeliharaan, layanan online tidak dapat diakses. Namun, layanan offline di kantor pelayanan tetap beroperasi seperti biasa.</p>

<h2>Rekomendasi</h2>
<p>Kami menyarankan pengguna untuk:</p>
<ul>
<li>Menyelesaikan pengajuan permohonan sebelum jadwal pemeliharaan</li>
<li>Tidak melakukan submit data pada waktu yang ditentukan</li>
<li>Menyimpan draft permohonan untuk menghindari kehilangan data</li>
</ul>

<p>Demikian pemberitahuan ini kami sampaikan. Atas perhatian dan pengertian Bapak/Ibu, kami ucapkan terima kasih.</p>

<p><em>Untuk keadaan darurat, silakan hubungi tim support di support@perijinan.go.id</em></p>',
                'gambar' => null,
                'status' => 'tidak_aktif',
                'user_id' => $user->id,
                'views' => 45,
            ],
            [
                'judul' => 'Capaian Pelayanan Perizinan Tahun 2025',
                'slug' => 'capaian-pelayanan-perizinan-tahun-2025',
                'konten' => '<p>Pemerintah mengumumkan capaian pelayanan perizinan sepanjang tahun 2025. Berbagai peningkatan signifikan telah dicapai dalam upaya meningkatkan efisiensi dan kualitas pelayanan.</p>

<h2>Statistik 2025</h2>
<p>Berikut adalah beberapa capaian penting:</p>
<ul>
<li><strong>Total perizinan diproses:</strong> 50.000+</li>
<li><strong>Waktu rata-rata penyelesaian:</strong> 3 hari kerja</li>
<li><strong>Tingkat kepuasan masyarakat:</strong> 92%</li>
<li><strong>Perizinan online:</strong> 85% dari total permohonan</li>
</ul>

<h2>Penghargaan</h2>
<p>Atas capaian tersebut, kantor pelayanan perizinan menerima penghargaan sebagai <strong>Layanan Publik Terbaik 2025</strong> dari Kementerian PAN-RB.</p>

<blockquote>"Ini adalah hasil kerja keras seluruh tim dan dukungan dari masyarakat. Kami akan terus berkomitmen untuk memberikan pelayanan terbaik."</blockquote>

<h2>Rencana 2026</h2>
<p>Pada tahun 2026, pemerintah menargetkan:</p>
<ol>
<li>100% perizinan dapat diakses secara online</li>
<li>Waktu penyelesaian rata-rata 2 hari kerja</li>
<li>Integrasi dengan sistem nasional</li>
<li>Penambahan jenis perizinan yang dapat diakses online</li>
</ol>

<p>Kami mengucapkan terima kasih kepada seluruh masyarakat atas kepercayaan dan kerjasamanya. Mari bersama kita wujudkan pelayanan publik yang lebih baik.</p>',
                'gambar' => null,
                'status' => 'aktif',
                'user_id' => $user->id,
                'views' => 312,
            ],
        ];

        foreach ($beritas as $berita) {
            Berita::create($berita);
        }

        $this->command->info('5 artikel berita dummy berhasil ditambahkan.');
    }
}
