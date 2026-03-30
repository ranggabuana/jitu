<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OpdUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Operator OPD users
        User::firstOrCreate(
            ['username' => 'operator1'],
            [
                'name' => 'Budi Santoso',
                'email' => 'operator1@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator_opd',
                'status' => 'aktif',
                'nip' => '198501012010011001',
                'no_hp' => '081234567890',
            ]
        );

        User::firstOrCreate(
            ['username' => 'operator2'],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'operator2@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator_opd',
                'status' => 'aktif',
                'nip' => '198702022012022002',
                'no_hp' => '081234567891',
            ]
        );

        User::firstOrCreate(
            ['username' => 'operator3'],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'operator3@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator_opd',
                'status' => 'aktif',
                'nip' => '198903032014031003',
                'no_hp' => '081234567892',
            ]
        );

        // Kepala OPD users
        User::firstOrCreate(
            ['username' => 'kepala1'],
            [
                'name' => 'Dr. Siti Aminah',
                'email' => 'kepala1@example.com',
                'password' => Hash::make('password'),
                'role' => 'kepala_opd',
                'status' => 'aktif',
                'nip' => '197001011995011001',
                'no_hp' => '081234567893',
            ]
        );

        User::firstOrCreate(
            ['username' => 'kepala2'],
            [
                'name' => 'Ir. Joko Widodo',
                'email' => 'kepala2@example.com',
                'password' => Hash::make('password'),
                'role' => 'kepala_opd',
                'status' => 'aktif',
                'nip' => '197202021997021002',
                'no_hp' => '081234567894',
            ]
        );

        User::firstOrCreate(
            ['username' => 'kepala3'],
            [
                'name' => 'Drs. H. Ahmad Dahlan',
                'email' => 'kepala3@example.com',
                'password' => Hash::make('password'),
                'role' => 'kepala_opd',
                'status' => 'aktif',
                'nip' => '197403031999031003',
                'no_hp' => '081234567895',
            ]
        );
    }
}
