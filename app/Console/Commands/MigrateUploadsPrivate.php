<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateUploadsPrivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:migrate-private {--delete-source : Delete files from public folder after successful copy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate sensitive upload folders from public/uploads to storage/app/uploads';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folders = [
            'register',
            'dokumen_pemohon',
            'pembetulan_old',
            'perijinan',
            'data-perijinan',
            'templates'
        ];

        $this->info('Starting migration of sensitive uploads to private storage...');

        foreach ($folders as $folder) {
            $sourcePath = public_path('uploads/' . $folder);
            $destinationPath = storage_path('app/uploads/' . $folder);

            if (!File::exists($sourcePath)) {
                $this->line("Source folder public/uploads/{$folder} does not exist. Skipping.");
                continue;
            }

            $this->info("Migrating folder: {$folder}");

            // Ensure destination folder exists
            if (!File::exists(dirname($destinationPath))) {
                File::makeDirectory(dirname($destinationPath), 0755, true, true);
            }

            // Copy directory
            if (File::copyDirectory($sourcePath, $destinationPath)) {
                $this->info("Successfully copied {$folder} to private storage.");

                if ($this->option('delete-source')) {
                    File::deleteDirectory($sourcePath);
                    $this->info("Deleted public source folder: public/uploads/{$folder}");
                }
            } else {
                $this->error("Failed to copy folder: {$folder}");
            }
        }

        $this->info('Uploads migration completed.');
        return 0;
    }
}
