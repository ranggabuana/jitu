<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataPerijinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileAccessController extends Controller
{
    /**
     * Serve sensitive uploads securely after checking permissions.
     */
    public function serve(Request $request, $filepath)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Clean filepath to prevent directory traversal
        $filepath = urldecode($filepath);
        $filepath = str_replace('..', '', $filepath);
        $filepath = ltrim($filepath, '/');

        // Force prepend 'uploads/' if missing to ensure we only access uploads folder
        if (!str_starts_with($filepath, 'uploads/')) {
            $filepath = 'uploads/' . $filepath;
        }

        $fullPath = storage_path('app/' . $filepath);

        if (!File::exists($fullPath)) {
            abort(404, 'File tidak ditemukan.');
        }

        // 1. Admin/Superadmin bypass
        if ($user->isAdmin()) {
            return response()->file($fullPath);
        }

        // 2. Validate Pemohon Access
        $pathParts = explode('/', $filepath);
        $subfolder = $pathParts[1] ?? '';

        switch ($subfolder) {
            case 'register':
                // Check if the uploader is the current user
                $owner = User::where('foto_ktp', $filepath)->first();
                if ($owner && $owner->id === $user->id) {
                    return response()->file($fullPath);
                }
                break;

            case 'dokumen_pemohon':
                // Folder structure is: uploads/dokumen_pemohon/{userId}/{filename}
                $userIdFromPath = $pathParts[2] ?? null;
                if ($userIdFromPath && (int)$userIdFromPath === $user->id) {
                    return response()->file($fullPath);
                }
                break;

            case 'pembetulan_old':
                // Folder structure: uploads/pembetulan_old/{applicationId}_...
                $filename = basename($filepath);
                $appId = (int)explode('_', $filename)[0];
                $app = DataPerijinan::find($appId);
                if ($app && $app->user_id === $user->id) {
                    return response()->file($fullPath);
                }
                break;

            case 'perijinan':
                // Folder structure is: uploads/perijinan/{perijinanId}/{filename}
                $perijinanId = $pathParts[2] ?? null;
                if ($perijinanId) {
                    // Check if user has an application for this perijinan type
                    $hasApp = DataPerijinan::where('user_id', $user->id)
                        ->where('perijinan_id', $perijinanId)
                        ->exists();
                    if ($hasApp) {
                        return response()->file($fullPath);
                    }
                }
                break;

            case 'data-perijinan':
                // Workflow diagrams are general/public to all logged-in users
                return response()->file($fullPath);

            default:
                // Block everything else for non-admins (e.g. templates)
                break;
        }

        abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
    }
}
