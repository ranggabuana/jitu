<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip' => null,
                'no_hp' => '081234567890',
                'status' => 'aktif',
                'opd_id' => null,
            ]
        );

        // Front Office
        User::firstOrCreate(
            ['email' => 'fo@example.com'],
            [
                'name' => 'Front Office',
                'username' => 'fo',
                'password' => Hash::make('password'),
                'role' => 'fo',
                'nip' => '198501012010012001',
                'no_hp' => '081234567891',
                'status' => 'aktif',
                'opd_id' => null,
            ]
        );

        // Back Office
        User::firstOrCreate(
            ['email' => 'bo@example.com'],
            [
                'name' => 'Back Office',
                'username' => 'bo',
                'password' => Hash::make('password'),
                'role' => 'bo',
                'nip' => '198602022011012001',
                'no_hp' => '081234567892',
                'status' => 'aktif',
                'opd_id' => null,
            ]
        );

        // Verifikator
        User::firstOrCreate(
            ['email' => 'verifikator@example.com'],
            [
                'name' => 'Verifikator',
                'username' => 'verifikator',
                'password' => Hash::make('password'),
                'role' => 'verifikator',
                'nip' => '198703032012011001',
                'no_hp' => '081234567893',
                'status' => 'aktif',
                'opd_id' => null,
            ]
        );

        // Kadin
        User::firstOrCreate(
            ['email' => 'kadin@example.com'],
            [
                'name' => 'Kepala Dinas',
                'username' => 'kadin',
                'password' => Hash::make('password'),
                'role' => 'kadin',
                'nip' => '197001011990011001',
                'no_hp' => '081234567894',
                'status' => 'aktif',
                'opd_id' => null,
            ]
        );

        // Get some OPDs for operator and kepala opd
        $opds = Opd::take(5)->get();

        // Operator OPD for each OPD
        foreach ($opds as $index => $opd) {
            User::firstOrCreate(
                ['email' => "operator.opd{$index}@example.com"],
                [
                    'name' => "Operator {$opd->nama_opd}",
                    'username' => "operator_opd_{$index}",
                    'password' => Hash::make('password'),
                    'role' => 'operator_opd',
                    'nip' => "19900101202001100{$index}",
                    'no_hp' => "08123456790{$index}",
                    'status' => 'aktif',
                    'opd_id' => $opd->id,
                ]
            );

            // Kepala OPD for each OPD
            User::firstOrCreate(
                ['email' => "kepala.opd{$index}@example.com"],
                [
                    'name' => "Kepala {$opd->nama_opd}",
                    'username' => "kepala_opd_{$index}",
                    'password' => Hash::make('password'),
                    'role' => 'kepala_opd',
                    'nip' => "19750101199501100{$index}",
                    'no_hp' => "08123456791{$index}",
                    'status' => 'aktif',
                    'opd_id' => $opd->id,
                ]
            );
        }

        $this->command->info('Data pengguna dummy berhasil ditambahkan.');
        $this->command->info('Login credentials:');
        $this->command->info('  - Admin: admin / password');
        $this->command->info('  - FO: fo / password');
        $this->command->info('  - BO: bo / password');
        $this->command->info('  - Verifikator: verifikator / password');
        $this->command->info('  - Kadin: kadin / password');
        $this->command->info('  - Operator OPD: operator_opd_0 / password');
        $this->command->info('  - Kepala OPD: kepala_opd_0 / password');
    }
}
