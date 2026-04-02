<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SettingsController extends Controller
{
    /**
     * Show database settings page.
     */
    public function database()
    {
        return view('settings.database');
    }

    /**
     * Show activity logs page.
     */
    public function logs(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $logName = $request->get('log_name', '');
        $event = $request->get('event', '');
        $search = $request->get('search', '');

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by log name
        if ($logName) {
            $query->where('log_name', $logName);
        }

        // Filter by event
        if ($event) {
            $query->where('event', $event);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate($perPage);

        $logNames = ActivityLog::select('log_name')
            ->whereNotNull('log_name')
            ->distinct()
            ->pluck('log_name');

        $events = ActivityLog::select('event')
            ->whereNotNull('event')
            ->distinct()
            ->pluck('event');

        return view('settings.logs', compact('logs', 'logNames', 'events', 'logName', 'event', 'search'));
    }

    /**
     * Backup database.
     */
    public function backupDatabase()
    {
        try {
            $filename = 'backup_db_' . Carbon::now()->format('Y-m-d_His') . '.sql';
            $path = public_path('backups/database/' . $filename);

            // Create backups directory if not exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Get database credentials
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            // Try to find mysqldump path (Laragon default)
            $mysqldumpPaths = [
                'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.44-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.26-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.21-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin\\mysqldump.exe',
                'mysqldump', // Fallback to PATH
            ];
            
            $mysqldumpPath = 'mysqldump';
            foreach ($mysqldumpPaths as $testPath) {
                if (file_exists($testPath)) {
                    $mysqldumpPath = $testPath;
                    break;
                }
            }
            
            // Run mysqldump
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s > %s',
                $mysqldumpPath,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($path)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && filesize($path) > 0) {
                return redirect()->back()
                    ->with('success', 'Backup database berhasil! File: ' . $filename);
            } else {
                $errorMsg = $returnCode !== 0 ? 'Kode error: ' . $returnCode : 'File kosong';
                return redirect()->back()
                    ->with('error', 'Backup database gagal. ' . $errorMsg . '. Pastikan mysqldump tersedia.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup database gagal: ' . $e->getMessage());
        }
    }

    /**
     * Backup aplikasi.
     */
    public function backupAplikasi()
    {
        try {
            $filename = 'backup_aplikasi_' . Carbon::now()->format('Y-m-d_His') . '.zip';
            $path = public_path('backups/aplikasi/' . $filename);

            // Create backups directory if not exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Create zip archive of entire application
            $zip = new \ZipArchive();
            if ($zip->open($path, \ZipArchive::CREATE) === TRUE) {
                // Add entire application folder
                $appPath = base_path();
                $this->addFolderToZip($zip, $appPath, 'app', true);
                $zip->close();
                
                return redirect()->back()
                    ->with('success', 'Backup aplikasi berhasil! File: ' . $filename);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membuat file zip.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup aplikasi gagal: ' . $e->getMessage());
        }
    }

    /**
     * Backup full application (database + aplikasi).
     */
    public function backupFull()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $backupDir = public_path('backups/full');

            // Create backup directory
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            // Get database credentials
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            // Try to find mysqldump path (Laragon default)
            $mysqldumpPaths = [
                'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.44-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.26-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.21-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin\\mysqldump.exe',
                'mysqldump',
            ];
            
            $mysqldumpPath = 'mysqldump';
            foreach ($mysqldumpPaths as $testPath) {
                if (file_exists($testPath)) {
                    $mysqldumpPath = $testPath;
                    break;
                }
            }
            
            // Backup database
            $dbFilename = 'backup_db_' . $timestamp . '.sql';
            $dbPath = $backupDir . '/' . $dbFilename;
            
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s > %s',
                $mysqldumpPath,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($dbPath)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0 || filesize($dbPath) === 0) {
                return redirect()->back()
                    ->with('error', 'Backup database gagal.');
            }
            
            // Backup aplikasi
            $appZipFilename = 'backup_aplikasi_' . $timestamp . '.zip';
            $appZipPath = $backupDir . '/' . $appZipFilename;
            
            $zip = new \ZipArchive();
            if ($zip->open($appZipPath, \ZipArchive::CREATE) === TRUE) {
                $appPath = base_path();
                $this->addFolderToZip($zip, $appPath, 'aplikasi', true);
                $zip->close();
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membuat zip aplikasi.');
            }
            
            // Create full backup zip containing both
            $fullZipFilename = 'backup_full_' . $timestamp . '.zip';
            $fullZipPath = $backupDir . '/' . $fullZipFilename;
            
            $fullZip = new \ZipArchive();
            if ($fullZip->open($fullZipPath, \ZipArchive::CREATE) === TRUE) {
                // Add database dump
                $fullZip->addFile($dbPath, 'database/' . $dbFilename);
                
                // Add aplikasi zip
                $fullZip->addFile($appZipPath, 'aplikasi/' . $appZipFilename);
                
                $fullZip->close();
                
                // Remove temporary files
                unlink($dbPath);
                unlink($appZipPath);
                
                return redirect()->back()
                    ->with('success', 'Backup full berhasil! File: ' . $fullZipFilename);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membuat file zip full backup.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file.
     */
    public function downloadBackup($type, $filename)
    {
        $path = public_path('backups/' . $type . '/' . $filename);

        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()
            ->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Delete backup file.
     */
    public function deleteBackup($type, $filename)
    {
        try {
            $path = public_path('backups/' . $type . '/' . $filename);

            if (file_exists($path)) {
                unlink($path);
                return response()->json(['success' => true, 'message' => 'Backup berhasil dihapus.']);
            }
            
            return response()->json(['success' => false, 'message' => 'File backup tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus backup: ' . $e->getMessage()], 500);
        }
    }

    /**
     * List backup files.
     */
    public function listBackups($type)
    {
        $path = public_path('backups/' . $type);
        $files = [];

        if (file_exists($path)) {
            $scanFiles = scandir($path);
            foreach ($scanFiles as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $path . '/' . $file;
                    if (is_file($filePath)) {
                        $files[] = [
                            'name' => $file,
                            'size' => $this->formatSize(filesize($filePath)),
                            'date' => Carbon::createFromTimestamp(filemtime($filePath))->format('d M Y H:i'),
                        ];
                    }
                }
            }
        }
        
        return response()->json(['files' => $files]);
    }

    /**
     * Format file size.
     */
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Add folder to zip archive.
     */
    private function addFolderToZip($zip, $path, $zipPath = '', $excludeVendor = false)
    {
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    // Skip current and parent directory
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    
                    // Skip excluded folders for application backup
                    if ($excludeVendor && in_array($file, ['vendor', 'node_modules', 'backups', '.git', 'tests'])) {
                        continue;
                    }
                    
                    $filePath = $path . '/' . $file;
                    $zipFilePath = $zipPath ? $zipPath . '/' . $file : $file;
                    
                    if (is_dir($filePath)) {
                        $this->addFolderToZip($zip, $filePath, $zipFilePath, $excludeVendor);
                    } else {
                        $zip->addFile($filePath, $zipFilePath);
                    }
                }
                closedir($dh);
            }
        }
    }
}
