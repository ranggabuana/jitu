<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'status_pemohon',
        'nama_perusahaan',
        'npwp',
        'opd_id',
        'nip',
        'no_hp',
        'status',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'kelurahan_id',
        'alamat_lengkap',
        'foto_ktp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'role_label',
    ];

    /**
     * Get the OPD that the user belongs to.
     */
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    /**
     * Get the berita created by the user.
     */
    public function berita()
    {
        return $this->hasMany(Berita::class);
    }

    /**
     * Get the data SKM created by the user.
     */
    public function dataSkm()
    {
        return $this->hasMany(DataSkm::class);
    }

    /**
     * Get the hasil SKM submitted by the user.
     */
    public function hasilSkm()
    {
        return $this->hasMany(HasilSkm::class);
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has FO role.
     */
    public function isFo(): bool
    {
        return $this->role === 'fo';
    }

    /**
     * Check if user has BO role.
     */
    public function isBo(): bool
    {
        return $this->role === 'bo';
    }

    /**
     * Check if user has operator OPD role.
     */
    public function isOperatorOpd(): bool
    {
        return $this->role === 'operator_opd';
    }

    /**
     * Check if user has kepala OPD role.
     */
    public function isKepalaOpd(): bool
    {
        return $this->role === 'kepala_opd';
    }

    /**
     * Check if user has verifikator role.
     */
    public function isVerifikator(): bool
    {
        return $this->role === 'verifikator';
    }

    /**
     * Check if user has kadin role.
     */
    public function isKadin(): bool
    {
        return $this->role === 'kadin';
    }

    /**
     * Check if user is from OPD.
     */
    public function isOpdUser(): bool
    {
        return in_array($this->role, ['operator_opd', 'kepala_opd']);
    }

    /**
     * Get role label.
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin' => 'Admin',
            'fo' => 'Front Office',
            'bo' => 'Back Office',
            'operator_opd' => 'Operator OPD',
            'kepala_opd' => 'Kepala OPD',
            'verifikator' => 'Verifikator',
            'kadin' => 'Kadin',
        ];
        return $labels[$this->role] ?? $this->role;
    }

    /**
     * Get all role labels.
     */
    public static function getRoleLabels(): array
    {
        return [
            'admin' => 'Admin',
            'fo' => 'Front Office',
            'bo' => 'Back Office',
            'operator_opd' => 'Operator OPD',
            'kepala_opd' => 'Kepala OPD',
            'verifikator' => 'Verifikator',
            'kadin' => 'Kadin',
        ];
    }

    /**
     * Check if user can access admin-only menus.
     */
    public function canAccessAdminMenus(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can access data perijinan based on validation flow assignment.
     * This returns true for ALL roles except pemohon, but data will be filtered in controller.
     */
    public function canAccessDataPerijinan(): bool
    {
        // Pemohon cannot access data perijinan
        if ($this->role === 'pemohon') {
            return false;
        }

        // All other roles can access the menu (data will be filtered in controller)
        return true;
    }

    /**
     * Get list of perijinan IDs that user can access.
     * Logic:
     * - Admin: access to ALL perijinan
     * - FO, BO, Verifikator, Kadin: access to ALL perijinan with their role in validation flow
     * - Operator OPD, Kepala OPD: access ONLY to perijinan where they are specifically assigned
     */
    public function getAccessiblePerijinanIds(): array
    {
        // Admin can access all
        if ($this->role === 'admin') {
            return [];
        }

        // Roles that have access to ALL perijinan with their role type
        $collectiveRoles = ['fo', 'bo', 'verifikator', 'kadin'];
        
        if (in_array($this->role, $collectiveRoles)) {
            // Get all perijinan that have validation flow for this role
            $perijinanIds = PerijinanValidationFlow::whereIn('role', $collectiveRoles)
                ->where('is_active', true)
                ->pluck('perijinan_id')
                ->unique()
                ->values()
                ->toArray();
            
            \Log::info('Collective role access for user ' . $this->id . ' (role: ' . $this->role . '): ' . json_encode($perijinanIds));
            
            return $perijinanIds;
        }
        
        // Roles that need specific assignment (Operator OPD, Kepala OPD, etc.)
        // Get perijinan IDs where this specific user is assigned
        $perijinanIds = PerijinanValidationFlow::where('assigned_user_id', $this->id)
            ->where('is_active', true)
            ->get()
            ->pluck('perijinan_id')
            ->unique()
            ->values()
            ->toArray();
        
        \Log::info('Assigned role access for user ' . $this->id . ' (role: ' . $this->role . '): ' . json_encode($perijinanIds));
        
        return $perijinanIds;
    }

    /**
     * Check if user can access pengaduan menu.
     */
    public function canAccessPengaduanMenu(): bool
    {
        // Admin can always access
        if ($this->role === 'admin') {
            return true;
        }

        // Check using existing helper
        return \App\Models\PengaduanHandler::canAccessPengaduan();
    }
}
