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
    public function serve(Request $request, $secure_path = null)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Get filepath from query parameter if not provided in the route path
        $filepath = $secure_path ?: $request->query('filepath') ?: $request->query('path');

        if (!$filepath) {
            abort(400, 'Parameter filepath tidak ditentukan.');
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

        // 1. Admin/Superadmin/Staff bypass (roles allowed on the admin dashboard)
        if (in_array($user->role, ['admin', 'fo', 'bo', 'operator_opd', 'kepala_opd', 'verifikator', 'kadin'])) {
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
                if ($app) {
                    if ($app->user_id === $user->id) {
                        return response()->file($fullPath);
                    }
                    
                    // Also check OPD staff access
                    $accessibleIds = $user->getAccessiblePerijinanIds();
                    if (!empty($accessibleIds) && in_array($app->perijinan_id, $accessibleIds)) {
                        return response()->file($fullPath);
                    }
                }
                break;

            case 'perijinan':
                // Folder structure is: uploads/perijinan/{perijinanId}/{filename}
                // or: uploads/perijinan/generated_{no_registrasi}/{filename}
                $perijinanIdOrFolder = $pathParts[2] ?? null;
                if ($perijinanIdOrFolder) {
                    if (str_starts_with($perijinanIdOrFolder, 'generated_')) {
                        $safeNoRegistrasi = substr($perijinanIdOrFolder, 10);
                        $app = DataPerijinan::where('no_registrasi', str_replace('_', '-', $safeNoRegistrasi))
                            ->orWhere(\DB::raw("REPLACE(no_registrasi, '-', '_')"), $safeNoRegistrasi)
                            ->first();
                        if ($app) {
                            if ($app->user_id === $user->id) {
                                return response()->file($fullPath);
                            }
                            
                            // Check accessible perijinan IDs for OPD staff
                            $accessibleIds = $user->getAccessiblePerijinanIds();
                            if (!empty($accessibleIds) && in_array($app->perijinan_id, $accessibleIds)) {
                                return response()->file($fullPath);
                            }
                        }
                    } else {
                        // Check if user is pemohon and has an application for this perijinan type
                        $hasApp = DataPerijinan::where('user_id', $user->id)
                            ->where('perijinan_id', $perijinanIdOrFolder)
                            ->exists();
                        if ($hasApp) {
                            return response()->file($fullPath);
                        }
                        
                        // Check using getAccessiblePerijinanIds for OPD staff
                        $accessibleIds = $user->getAccessiblePerijinanIds();
                        if (!empty($accessibleIds) && in_array((int)$perijinanIdOrFolder, $accessibleIds)) {
                            return response()->file($fullPath);
                        }
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
