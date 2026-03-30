<?php

namespace Database\Seeders;

use App\Models\DataSkm;
use App\Models\HasilSkm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => bcrypt('password'),
            ]);
        }

        $questions = [
            [
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap kualitas pelayanan yang diberikan?',
                'urutan' => 1,
                'status' => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap kecepatan pelayanan?',
                'urutan' => 2,
                'status' => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap keramahan petugas pelayanan?',
                'urutan' => 3,
                'status' => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap kebersihan dan kenyamanan ruang pelayanan?',
                'urutan' => 4,
                'status' => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap kemudahan akses informasi pelayanan?',
                'urutan' => 5,
                'status' => 'aktif',
            ],
            [
                'pertanyaan' => 'Apakah Anda akan merekomendasikan layanan kami kepada orang lain?',
                'urutan' => 6,
                'status' => 'aktif',
            ],
        ];

        $questionIds = [];
        foreach ($questions as $question) {
            $question['user_id'] = $user->id;
            $question['bobot_max'] = 4;
            $created = DataSkm::create($question);
            $questionIds[] = $created->id;
        }

        $this->command->info('6 pertanyaan SKM dummy berhasil ditambahkan.');

        // Data dummy responden
        $responden = [
            ['nama' => 'Budi Santoso', 'email' => 'budi.santoso@email.com', 'nip' => null],
            ['nama' => 'Siti Aminah', 'email' => 'siti.aminah@email.com', 'nip' => null],
            ['nama' => 'Ahmad Hidayat', 'email' => 'ahmad.hidayat@email.com', 'nip' => '198501012010011001'],
            ['nama' => 'Dewi Lestari', 'email' => 'dewi.lestari@email.com', 'nip' => null],
            ['nama' => 'Muhammad Rizki', 'email' => 'rizki.m@email.com', 'nip' => null],
            ['nama' => 'Rina Wulandari', 'email' => 'rina.w@email.com', 'nip' => '199002022015022001'],
            ['nama' => 'Andi Pratama', 'email' => 'andi.pratama@email.com', 'nip' => null],
            ['nama' => 'Fitri Handayani', 'email' => 'fitri.h@email.com', 'nip' => null],
            ['nama' => 'Doni Setiawan', 'email' => 'doni.setiawan@email.com', 'nip' => '198803032012031001'],
            ['nama' => 'Anonim', 'email' => null, 'nip' => null],
        ];

        // Data jawaban untuk setiap responden (6 jawaban untuk 6 pertanyaan)
        $jawabanData = [
            // Budi - Puas
            ['jawaban' => '4', 'saran' => 'Pelayanan sudah sangat baik, pertahankan!'],
            ['jawaban' => '4', 'saran' => 'Proses cepat dan efisien'],
            ['jawaban' => '4', 'saran' => 'Petugas sangat ramah dan membantu'],
            ['jawaban' => '3', 'saran' => 'Ruang tunggu bisa lebih nyaman'],
            ['jawaban' => '4', 'saran' => 'Informasi jelas dan mudah dipahami'],
            ['jawaban' => '4', 'saran' => 'Sangat puas, akan rekomendasikan ke teman'],
            
            // Siti - Cukup Puas
            ['jawaban' => '3', 'saran' => 'Secara keseluruhan sudah baik'],
            ['jawaban' => '3', 'saran' => 'Perlu dipercepat lagi prosesnya'],
            ['jawaban' => '4', 'saran' => 'Petugas sangat ramah'],
            ['jawaban' => '3', 'saran' => 'Kebersihan sudah cukup baik'],
            ['jawaban' => '3', 'saran' => 'Website bisa lebih user friendly'],
            ['jawaban' => '4', 'saran' => 'Puas dengan pelayanan'],
            
            // Ahmad - Sangat Puas (Internal)
            ['jawaban' => '4', 'saran' => 'Excellent service!'],
            ['jawaban' => '4', 'saran' => 'Sangat cepat'],
            ['jawaban' => '4', 'saran' => 'Profesional'],
            ['jawaban' => '4', 'saran' => 'Fasilitas lengkap'],
            ['jawaban' => '4', 'saran' => 'Informasi tersedia dengan baik'],
            ['jawaban' => '4', 'saran' => 'Sangat merekomendasikan'],
            
            // Dewi - Kurang Puas
            ['jawaban' => '2', 'saran' => 'Perlu peningkatan kualitas'],
            ['jawaban' => '2', 'saran' => 'Terlalu lama, perlu antrian lebih baik'],
            ['jawaban' => '3', 'saran' => 'Petugas cukup ramah'],
            ['jawaban' => '2', 'saran' => 'Ruang tunggu panas, AC kurang dingin'],
            ['jawaban' => '2', 'saran' => 'Informasi kurang jelas'],
            ['jawaban' => '2', 'saran' => 'Masih ada yang perlu diperbaiki'],
            
            // Rizki - Puas
            ['jawaban' => '4', 'saran' => 'Bagus sekali'],
            ['jawaban' => '3', 'saran' => 'Cukup cepat'],
            ['jawaban' => '4', 'saran' => 'Ramah dan profesional'],
            ['jawaban' => '3', 'saran' => 'Bersih dan nyaman'],
            ['jawaban' => '3', 'saran' => 'Cukup mudah diakses'],
            ['jawaban' => '4', 'saran' => 'Akan rekomendasikan'],
            
            // Rina - Internal, Puas
            ['jawaban' => '4', 'saran' => 'Pelayanan prima'],
            ['jawaban' => '4', 'saran' => 'Efisien'],
            ['jawaban' => '4', 'saran' => 'Sangat membantu'],
            ['jawaban' => '3', 'saran' => 'Parkir agak sempit'],
            ['jawaban' => '4', 'saran' => 'Sosialisasi sudah baik'],
            ['jawaban' => '4', 'saran' => 'Luar biasa'],
            
            // Andi - Cukup Puas
            ['jawaban' => '3', 'saran' => 'Sudah oke'],
            ['jawaban' => '3', 'saran' => 'Standard'],
            ['jawaban' => '3', 'saran' => 'Baik'],
            ['jawaban' => '3', 'saran' => 'Cukup bersih'],
            ['jawaban' => '3', 'saran' => 'Lumayan'],
            ['jawaban' => '3', 'saran' => 'Oke'],
            
            // Fitri - Sangat Puas
            ['jawaban' => '4', 'saran' => 'Terima kasih atas pelayanannya'],
            ['jawaban' => '4', 'saran' => 'Cepat sekali'],
            ['jawaban' => '4', 'saran' => 'Senyum petugas membuat nyaman'],
            ['jawaban' => '4', 'saran' => 'Seperti di mall'],
            ['jawaban' => '4', 'saran' => 'Mudah dipahami'],
            ['jawaban' => '4', 'saran' => 'Top markotop'],
            
            // Doni - Internal, Cukup Puas
            ['jawaban' => '3', 'saran' => 'Perlu ditingkatkan lagi'],
            ['jawaban' => '3', 'saran' => 'Masih bisa lebih cepat'],
            ['jawaban' => '4', 'saran' => 'Rekan-rekan sangat membantu'],
            ['jawaban' => '3', 'saran' => 'Fasilitas cukup'],
            ['jawaban' => '3', 'saran' => 'Perlu update berkala'],
            ['jawaban' => '3', 'saran' => 'Good enough'],
            
            // Anonim - Kurang Puas
            ['jawaban' => '2', 'saran' => 'Kecewa dengan pelayanan'],
            ['jawaban' => '1', 'saran' => 'Terlalu lama, hampir 3 jam menunggu'],
            ['jawaban' => '2', 'saran' => 'Petugas kurang informatif'],
            ['jawaban' => '2', 'saran' => 'Toilet kotor'],
            ['jawaban' => '1', 'saran' => 'Website sering error'],
            ['jawaban' => '2', 'saran' => 'Belum puas'],
        ];

        // Create hasil SKM for each respondent
        $now = Carbon::now();
        foreach ($responden as $index => $resp) {
            $startJawaban = $index * 6;
            for ($i = 0; $i < 6; $i++) {
                HasilSkm::create([
                    'data_skm_id' => $questionIds[$i],
                    'responden_nama' => $resp['nama'],
                    'responden_email' => $resp['email'],
                    'nip' => $resp['nip'],
                    'jawaban' => $jawabanData[$startJawaban + $i]['jawaban'],
                    'saran' => $jawabanData[$startJawaban + $i]['saran'],
                    'ip_address' => '192.168.1.' . rand(1, 254),
                    'user_id' => $index % 2 == 0 ? $user->id : null, // Some filled by logged-in user
                    'created_at' => $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        $this->command->info('60 jawaban SKM dummy berhasil ditambahkan.');
    }
}
