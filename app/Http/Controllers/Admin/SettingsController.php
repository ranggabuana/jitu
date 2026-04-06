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
     * Export activity logs to Excel.
     */
    public function exportLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Apply same filters as index
        $logName = $request->get('log_name', '');
        $event = $request->get('event', '');
        $search = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

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

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->get();

        // Generate filename with date range
        $filename = 'activity_logs';
        if ($dateFrom || $dateTo) {
            $filename .= '_';
            if ($dateFrom) {
                $filename .= $dateFrom;
            }
            $filename .= '_sd_';
            if ($dateTo) {
                $filename .= $dateTo;
            }
        }
        $filename .= '_' . date('Y-m-d_His') . '.xls';

        // Set headers for Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">';
        
        echo '<Styles>
            <Style ss:ID="Default" ss:Name="Normal">
                <Alignment ss:Vertical="Bottom"/>
                <Borders/>
                <Font ss:FontName="Calibri" ss:Size="11"/>
                <Interior/>
                <NumberFormat/>
                <Protection/>
            </Style>
            <Style ss:ID="header">
                <Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1" ss:Color="#FFFFFF"/>
                <Interior ss:Color="#6366F1" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="title">
                <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1F4E79"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="subtitle">
                <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#595959"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="date">
                <NumberFormat ss:Format="dd/mm/yyyy\ hh:mm"/>
            </Style>
            <Style ss:ID="wrap">
                <Alignment ss:Vertical="Center" ss:WrapText="1"/>
            </Style>
        </Styles>';

        echo '<Worksheet ss:Name="Activity Logs">';
        echo '<Table>';
        echo '<Column ss:Width="40"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="300"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="120"/>';
        
        // Title row
        echo '<Row ss:Height="30">';
        echo '<Cell ss:MergeAcross="7" ss:StyleID="title"><Data ss:Type="String">LAPORAN LOG AKTIVITAS</Data></Cell>';
        echo '</Row>';
        
        // Date range row
        echo '<Row ss:Height="20">';
        $dateRangeText = 'Periode: ';
        if ($dateFrom && $dateTo) {
            $dateRangeText .= date('d/m/Y', strtotime($dateFrom)) . ' s/d ' . date('d/m/Y', strtotime($dateTo));
        } elseif ($dateFrom) {
            $dateRangeText .= 'Dari tanggal ' . date('d/m/Y', strtotime($dateFrom)) . ' s/d sekarang';
        } elseif ($dateTo) {
            $dateRangeText .= 'Sampai tanggal ' . date('d/m/Y', strtotime($dateTo));
        } else {
            $dateRangeText .= 'Semua tanggal';
        }
        echo '<Cell ss:MergeAcross="7" ss:StyleID="subtitle"><Data ss:Type="String">' . $dateRangeText . '</Data></Cell>';
        echo '</Row>';
        
        // Empty row
        echo '<Row></Row>';
        
        // Header row
        echo '<Row ss:Height="25">';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Waktu</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Deskripsi</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">User</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Event</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Kategori</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Subject</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">IP Address</Data></Cell>';
        echo '</Row>';

        // Data rows
        $no = 1;
        foreach ($logs as $log) {
            echo '<Row>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="Number">' . $no++ . '</Data></Cell>';
            echo '<Cell ss:StyleID="date"><Data ss:Type="String">' . $log->created_at . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->description) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->user->name ?? 'Sistem') . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->event_label) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->log_name) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->subject_label ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->ip_address ?? '-') . '</Data></Cell>';
            echo '</Row>';
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        
        exit;
    }

    /**
     * Backup database.
     */
    public function backupDatabase()
    {
        try {
            // Check if exec is disabled
            if (in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                return redirect()->back()
                    ->with('error', 'Fungsi exec() dinonaktifkan oleh hosting. Hubungi admin hosting.');
            }

            $filename = 'backup_db_' . Carbon::now()->format('Y-m-d_His') . '.sql';
            $path = public_path('backups/database/' . $filename);

            // Create backups directory if not exists
            if (!file_exists(dirname($path))) {
                if (!mkdir(dirname($path), 0755, true)) {
                    return redirect()->back()
                        ->with('error', 'Gagal membuat folder backup. Cek permission folder.');
                }
            }

            // Check if directory is writable
            if (!is_writable(dirname($path))) {
                return redirect()->back()
                    ->with('error', 'Folder backup tidak writable. Cek permission (harus 755).');
            }

            // Get database credentials
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Try to find mysqldump path (Linux/Windows)
            $mysqldumpPaths = [
                // Windows (Laragon)
                'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.44-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.26-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-5.7.21-winx64\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin\\mysqldump.exe',
                // Linux common paths
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                '/usr/local/mysql/bin/mysqldump',
                '/opt/lampp/bin/mysqldump',
                '/Applications/MAMP/Library/bin/mysqldump',
                'mysqldump', // Fallback to PATH
            ];

            $mysqldumpPath = 'mysqldump';
            foreach ($mysqldumpPaths as $testPath) {
                if (file_exists($testPath) && is_executable($testPath)) {
                    $mysqldumpPath = $testPath;
                    break;
                }
            }

            // Build command
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg($mysqldumpPath),
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($path)
            );

            // Log command for debugging (remove in production)
            \Log::info('Backup command: ' . $command);

            // Run mysqldump
            exec($command, $output, $returnCode);

            // Log result
            \Log::info('Backup result', [
                'returnCode' => $returnCode,
                'output' => implode("\n", $output),
                'fileExists' => file_exists($path),
                'fileSize' => file_exists($path) ? filesize($path) : 0
            ]);

            if ($returnCode === 0 && file_exists($path) && filesize($path) > 0) {
                return redirect()->back()
                    ->with('success', 'Backup database berhasil! File: ' . $filename);
            } else {
                $errorMsg = $returnCode !== 0 ? 'Kode error: ' . $returnCode : 'File kosong';
                $outputMsg = !empty($output) ? ' Output: ' . implode(' ', $output) : '';
                
                \Log::error('Backup failed', [
                    'returnCode' => $returnCode,
                    'output' => $outputMsg
                ]);
                
                return redirect()->back()
                    ->with('error', 'Backup database gagal. ' . $errorMsg . $outputMsg);
            }
        } catch (\Exception $e) {
            \Log::error('Backup exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
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
