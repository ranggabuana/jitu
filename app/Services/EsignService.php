<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EsignService
{
    private $baseUrl;
    private $username;
    private $password;

    public function __construct()
    {
        $this->baseUrl = config('services.esign.url');
        $this->username = config('services.esign.username');
        $this->password = config('services.esign.password');
    }

    /**
     * Get instance
     */
    private static function getInstance()
    {
        return new self();
    }

    /**
     * Sign a PDF file using INVISIBLE signature
     * Returns the signed PDF content or throws Exception
     */
    public static function signPdf($nik, $passphrase, $filePath)
    {
        $instance = self::getInstance();
        
        if (!file_exists($filePath)) {
            throw new \Exception("File PDF tidak ditemukan: " . $filePath);
        }

        $fileBase64 = base64_encode(file_get_contents($filePath));
        
        $payload = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [
                [
                    'tampilan' => 'INVISIBLE'
                ]
            ],
            'file' => [$fileBase64]
        ];

        try {
            $response = Http::withBasicAuth($instance->username, $instance->password)
                ->timeout(120)
                ->post($instance->baseUrl . '/sign/pdf', $payload);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                
                // Sometimes API returns JSON with fileBase64
                if (str_contains($contentType, 'application/json')) {
                    $data = $response->json();
                    
                    // BSrE API v2 typically wraps the response in a 'data' key:
                    // {"success": true, "code": 200, "data": {"file": "base64..."}}
                    if (isset($data['data']['file']) && is_array($data['data']['file'])) {
                        return base64_decode($data['data']['file'][0]);
                    }
                    if (isset($data['data']['file'])) {
                        return base64_decode($data['data']['file']);
                    }

                    // Fallback to direct 'file' key if present
                    if (isset($data['file']) && is_array($data['file'])) {
                        return base64_decode($data['file'][0]);
                    }
                    if (isset($data['file'])) {
                        return base64_decode($data['file']);
                    }
                    
                    // If no file property but successful, maybe just raw
                    return $response->body();
                }

                // If binary or other format
                return $response->body();
            } else {
                $errorMsg = 'Gagal melakukan TTE. HTTP: ' . $response->status();
                $data = $response->json();
                if (isset($data['error'])) {
                    $errorMsg .= ' - ' . $data['error'];
                }
                throw new \Exception($errorMsg);
            }
        } catch (\Exception $e) {
            Log::error('EsignService::signPdf Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a PDF file
     */
    public static function verifyPdf($filePath)
    {
        $instance = self::getInstance();

        if (!file_exists($filePath)) {
            throw new \Exception("File PDF tidak ditemukan: " . $filePath);
        }

        $fileBase64 = base64_encode(file_get_contents($filePath));

        try {
            $response = Http::withBasicAuth($instance->username, $instance->password)
                ->timeout(60)
                ->post($instance->baseUrl . '/verify/pdf', [
                    'file' => $fileBase64
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => true,
                'message' => 'Gagal memverifikasi dokumen. HTTP: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('EsignService::verifyPdf Error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Terjadi kesalahan saat memverifikasi dokumen.'
            ];
        }
    }
}
