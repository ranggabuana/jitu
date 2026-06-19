<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perijinan extends Model
{
    protected $table = 'perijinan';

    protected $fillable = [
        'kode_perijinan',
        'nama_perijinan',
        'is_multi_opd',
        'has_bo_form',
        'opsi_perpanjangan',
        'dasar_hukum',
        'persyaratan',
        'prosedur',
        'informasi_biaya',
        'lama_waktu_proses',
        'gambar_alur',
        'template_pernyataan',
        'template_permohonan',
        'template_keabsahan',
        'template_surat_rekom',
        'template_surat_izin',
        'keterangan_rekom',
        'keterangan_izin',
        'next_nomor_rekom',
        'next_nomor_izin',
    ];

    protected $casts = [
        'is_multi_opd' => 'boolean',
        'has_bo_form' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Cascade delete related records
        static::deleting(function ($perijinan) {
            // 1. Delete form fields
            $perijinan->formFields()->delete();

            // 2. Delete validation flows
            $perijinan->validationFlows()->delete();

            // 3. Delete applications (triggers cascading)
            $perijinan->applications->each->delete();

            // 4. Delete image if exists
            if ($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur))) {
                @unlink(public_path($perijinan->gambar_alur));
            }
        });
    }

    /**
     * Get all form fields for this perijinan.
     */
    public function formFields(): HasMany
    {
        return $this->hasMany(PerijinanFormField::class)->orderBy('order');
    }

    /**
     * Get active form fields for this perijinan.
     */
    public function activeFormFields(): HasMany
    {
        return $this->hasMany(PerijinanFormField::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Get all validation flows for this perijinan.
     */
    public function validationFlows(): HasMany
    {
        return $this->hasMany(PerijinanValidationFlow::class)->orderBy('order');
    }

    /**
     * Get active validation flows for this perijinan.
     */
    public function activeValidationFlows(): HasMany
    {
        return $this->hasMany(PerijinanValidationFlow::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Get all applications for this perijinan (Primary).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(DataPerijinan::class, 'perijinan_id');
    }

    /**
     * Get all OPD configurations for this perijinan.
     */
    public function opdConfigs(): HasMany
    {
        return $this->hasMany(PerijinanOpdConfig::class, 'perijinan_id');
    }
}
