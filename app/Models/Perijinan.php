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
    ];

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
}
