<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data wilayah Indonesia...');

        // Clear existing data
        $this->command->info('Membersihkan data existing...');
        
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('kelurahans')->truncate();
        DB::table('kecamatans')->truncate();
        DB::table('kabupatens')->truncate();
        DB::table('provinsis')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Parse SQL file
        $this->command->info('Membaca file SQL...');
        $sqlFile = base_path('wilayah_indo.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('File wilayah_indo.sql tidak ditemukan!');
            return;
        }

        $sqlContent = file_get_contents($sqlFile);

        // Parse Provinsi
        $this->command->info('Memproses data provinsi...');
        $this->seedProvinsi($sqlContent);

        // Parse Kabupaten
        $this->command->info('Memproses data kabupaten...');
        $this->seedKabupaten($sqlContent);

        // Parse Kecamatan
        $this->command->info('Memproses data kecamatan...');
        $this->seedKecamatan($sqlContent);

        // Parse Desa/Kelurahan
        $this->command->info('Memproses data desa/kelurahan...');
        $this->seedDesa($sqlContent);

        $this->command->info('Seeding data wilayah selesai!');
    }

    /**
     * Seed provinsi data
     */
    private function seedProvinsi($sqlContent)
    {
        // Extract INSERT statements for provinsi
        preg_match_all("/INSERT INTO `wilayah_provinsi`.*?VALUES\s*(\(.*?\));/s", $sqlContent, $matches);
        
        if (empty($matches[1])) {
            $this->command->warn('Tidak ada data provinsi ditemukan.');
            return;
        }

        $data = [];
        foreach ($matches[1] as $valueGroup) {
            // Parse values - each row is ('id', 'nama')
            preg_match_all("/\('([^']+)'\s*,\s*'([^']+)'\)/", $valueGroup, $rowMatches);
            
            for ($i = 0; $i < count($rowMatches[1]); $i++) {
                $data[] = [
                    'code' => $rowMatches[1][$i],
                    'name' => trim($rowMatches[2][$i]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($data, 500);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            DB::table('provinsis')->insert($chunk);
            $totalInserted += count($chunk);
        }

        $this->command->info("✓ {$totalInserted} provinsi berhasil di-seed.");
    }

    /**
     * Seed kabupaten data
     */
    private function seedKabupaten($sqlContent)
    {
        preg_match_all("/INSERT INTO `wilayah_kabupaten`.*?VALUES\s*(\(.*?\));/s", $sqlContent, $matches);
        
        if (empty($matches[1])) {
            $this->command->warn('Tidak ada data kabupaten ditemukan.');
            return;
        }

        // Get all provinsi codes to map with IDs
        $provinsiMap = DB::table('provinsis')->pluck('id', 'code')->toArray();

        $data = [];
        foreach ($matches[1] as $valueGroup) {
            preg_match_all("/\('([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\)/", $valueGroup, $rowMatches);
            
            for ($i = 0; $i < count($rowMatches[1]); $i++) {
                $provinsiId = $provinsiMap[$rowMatches[2][$i]] ?? null;
                
                if ($provinsiId) {
                    $data[] = [
                        'code' => $rowMatches[1][$i],
                        'provinsi_id' => $provinsiId,
                        'name' => trim($rowMatches[3][$i]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        $chunks = array_chunk($data, 500);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            DB::table('kabupatens')->insert($chunk);
            $totalInserted += count($chunk);
        }

        $this->command->info("✓ {$totalInserted} kabupaten berhasil di-seed.");
    }

    /**
     * Seed kecamatan data
     */
    private function seedKecamatan($sqlContent)
    {
        preg_match_all("/INSERT INTO `wilayah_kecamatan`.*?VALUES\s*(\(.*?\));/s", $sqlContent, $matches);
        
        if (empty($matches[1])) {
            $this->command->warn('Tidak ada data kecamatan ditemukan.');
            return;
        }

        // Get all kabupaten codes to map with IDs
        $kabupatenMap = DB::table('kabupatens')->pluck('id', 'code')->toArray();

        $data = [];
        foreach ($matches[1] as $valueGroup) {
            preg_match_all("/\('([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\)/", $valueGroup, $rowMatches);
            
            for ($i = 0; $i < count($rowMatches[1]); $i++) {
                $kabupatenId = $kabupatenMap[$rowMatches[2][$i]] ?? null;
                
                if ($kabupatenId) {
                    $data[] = [
                        'code' => $rowMatches[1][$i],
                        'kabupaten_id' => $kabupatenId,
                        'name' => trim($rowMatches[3][$i]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        $chunks = array_chunk($data, 500);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            DB::table('kecamatans')->insert($chunk);
            $totalInserted += count($chunk);
        }

        $this->command->info("✓ {$totalInserted} kecamatan berhasil di-seed.");
    }

    /**
     * Seed desa/kelurahan data
     */
    private function seedDesa($sqlContent)
    {
        preg_match_all("/INSERT INTO `wilayah_desa`.*?VALUES\s*(\(.*?\));/s", $sqlContent, $matches);
        
        if (empty($matches[1])) {
            $this->command->warn('Tidak ada data desa ditemukan.');
            return;
        }

        // Get all kecamatan codes to map with IDs
        $kecamatanMap = DB::table('kecamatans')->pluck('id', 'code')->toArray();

        $data = [];
        foreach ($matches[1] as $valueGroup) {
            preg_match_all("/\('([^']+)'\s*,\s*'([^']*)'\s*,\s*'([^']+)'\)/", $valueGroup, $rowMatches);
            
            for ($i = 0; $i < count($rowMatches[1]); $i++) {
                $kecamatanId = $kecamatanMap[$rowMatches[2][$i]] ?? null;
                
                if ($kecamatanId) {
                    $data[] = [
                        'code' => $rowMatches[1][$i],
                        'kecamatan_id' => $kecamatanId,
                        'name' => trim($rowMatches[3][$i]),
                        'postal_code' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        $chunks = array_chunk($data, 500);
        $totalInserted = 0;

        $totalChunks = count($chunks);
        foreach ($chunks as $index => $chunk) {
            DB::table('kelurahans')->insert($chunk);
            $totalInserted += count($chunk);
            
            // Progress indicator for large inserts
            if (($index + 1) % 20 == 0) {
                $this->command->info("  Progress: {$index}/{$totalChunks} chunks...");
            }
        }

        $this->command->info("✓ {$totalInserted} desa/kelurahan berhasil di-seed.");
    }
}
