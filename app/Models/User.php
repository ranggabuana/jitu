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
}
