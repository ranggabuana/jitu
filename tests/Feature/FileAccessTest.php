<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $testFilepath = 'uploads/register/ktp_test.jpg';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Write a fake file to the actual storage folder for testing
        $fullPath = storage_path('app/' . $this->testFilepath);
        $dir = dirname($fullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($fullPath, 'fake image content');
    }

    protected function tearDown(): void
    {
        // Clean up the fake file
        $fullPath = storage_path('app/' . $this->testFilepath);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
        parent::tearDown();
    }

    public function test_unauthenticated_user_cannot_access_secure_file()
    {
        $response = $this->get('/secure-file/' . $this->testFilepath);
        $response->assertRedirect('/login'); // Redirected by auth middleware
    }

    public function test_admin_can_access_any_secure_file()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'admin_test',
        ]);

        $response = $this->actingAs($admin)
            ->get('/secure-file/' . $this->testFilepath);

        $response->assertStatus(200);
    }

    public function test_fo_staff_can_access_secure_file()
    {
        $foUser = User::factory()->create([
            'role' => 'fo',
            'username' => 'fo_test',
        ]);

        $response = $this->actingAs($foUser)
            ->get('/secure-file/' . $this->testFilepath);

        $response->assertStatus(200);
    }

    public function test_verifikator_staff_can_access_secure_file()
    {
        $verifikator = User::factory()->create([
            'role' => 'verifikator',
            'username' => 'verifikator_test',
        ]);

        $response = $this->actingAs($verifikator)
            ->get('/secure-file/' . $this->testFilepath);

        $response->assertStatus(200);
    }

    public function test_pemohon_owner_can_access_their_own_ktp()
    {
        $pemohon = User::factory()->create([
            'role' => 'pemohon',
            'username' => 'pemohon_test',
            'foto_ktp' => $this->testFilepath,
        ]);

        $response = $this->actingAs($pemohon)
            ->get('/secure-file/' . $this->testFilepath);

        $response->assertStatus(200);
    }

    public function test_pemohon_non_owner_cannot_access_other_ktp()
    {
        // Owner of the KTP
        $owner = User::factory()->create([
            'role' => 'pemohon',
            'username' => 'owner_test',
            'foto_ktp' => $this->testFilepath,
        ]);

        // Another pemohon user
        $otherPemohon = User::factory()->create([
            'role' => 'pemohon',
            'username' => 'other_pemohon_test',
        ]);

        $response = $this->actingAs($otherPemohon)
            ->get('/secure-file/' . $this->testFilepath);

        $response->assertStatus(403);
    }

    public function test_access_via_query_parameter()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'admin_query_test',
        ]);

        $response = $this->actingAs($admin)
            ->get('/secure-file?filepath=' . urlencode($this->testFilepath));

        $response->assertStatus(200);

        // Also test using path as query parameter name
        $response2 = $this->actingAs($admin)
            ->get('/secure-file?path=' . urlencode($this->testFilepath));

        $response2->assertStatus(200);
    }
}
