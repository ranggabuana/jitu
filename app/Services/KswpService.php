<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KswpService
{
    protected $baseUrl;
    protected $email;
    protected $password;
    protected $verifySsl;

    public function __construct()
    {
        $this->baseUrl = config('services.kswp.base_url');
        $this->email = config('services.kswp.email');
        $this->password = config('services.kswp.password');
        $this->verifySsl = config('services.kswp.verify_ssl');
    }

    /**
     * Get a valid token from cache or by logging in.
     */
    public function getToken()
    {
        $token = Cache::get('kswp_token');

        if (!$token || !$this->isTokenValid($token)) {
            $token = $this->login();
        }

        return $token;
    }

    /**
     * Login to KSWP API to get a new token.
     */
    protected function login()
    {
        try {
            $response = Http::withOptions(['verify' => $this->verifySsl])
                ->asForm()
                ->post("{$this->baseUrl}/login", [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

            if ($response->successful() && isset($response->json()['token'])) {
                $token = $response->json()['token'];
                // Store token in cache. We don't know the exact expiry, so let's store for 55 minutes
                // and we also have the validation check.
                Cache::put('kswp_token', $token, now()->addMinutes(55));
                return $token;
            }

            Log::error('KSWP Login Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('KSWP Login Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if the given token is still valid.
     */
    protected function isTokenValid($token)
    {
        try {
            $response = Http::withOptions(['verify' => $this->verifySsl])
                ->withToken($token)
                ->get("{$this->baseUrl}/cek_token");
            
            if ($response->successful()) {
                $data = $response->json();
                return isset($data['respon_code']) && $data['respon_code'] === 'OK';
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check tax status by NIK or NPWP.
     */
    public function checkTaxStatus($identifier, $type = 'NIK')
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'status' => 'ERROR',
                'message' => 'Gagal menghubungkan ke layanan KSWP.'
            ];
        }

        try {
            // According to common regional tax APIs, if the documentation only shows NIK, 
            // but needs to support NPWP, sometimes it's the same endpoint with a different key 
            // or the NIK key accepts NPWP.
            // But based on user request, I will use the specific key provided.
            $response = Http::withOptions(['verify' => $this->verifySsl])
                ->withToken($token)
                ->post("{$this->baseUrl}/getKSWPService", [
                    $type => $identifier
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('KSWP Check Tax Failed: ' . $response->body());
            return [
                'status' => 'ERROR',
                'message' => 'Gagal memeriksa status pajak.'
            ];
        } catch (\Exception $e) {
            Log::error('KSWP Check Tax Exception: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat menghubungi layanan KSWP.'
            ];
        }
    }
}
