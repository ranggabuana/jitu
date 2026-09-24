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
        'jenis_perijinan',
        'is_multi_opd',
        'has_bo_form',
        'validasi_tanpa_opd',
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
        'validasi_tanpa_opd' => 'boolean',
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

    /**
     * Check if a template type (rekom/izin) for this perijinan (and optional OPD) uses NOMOR_SURAT2.
     */
    public function usesNomorSurat2(string $type, $opdId = null): bool
    {
        $template = null;
        if ($type === 'rekom') {
            if ($opdId) {
                $opdConfig = $this->opdConfigs()->where('opd_id', $opdId)->first();
                if ($opdConfig && $opdConfig->template_surat_rekom) {
                    $template = $opdConfig->template_surat_rekom;
                }
            }
            if (!$template) {
                $template = $this->template_surat_rekom ?? \App\Models\Setting::get('template_rekom');
            }
        } else {
            $template = $this->template_surat_izin ?? \App\Models\Setting::get('template_izin');
        }

        if (empty($template)) {
            return false;
        }

        // If it's a docx file path, read from docx archive
        if (is_string($template) && str_ends_with($template, '.docx')) {
            $fullPath = public_path($template);
            if (file_exists($fullPath)) {
                $zip = new \ZipArchive();
                if ($zip->open($fullPath) === true) {
                    $xml = $zip->getFromName('word/document.xml');
                    $zip->close();
                    if ($xml !== false) {
                        return str_contains($xml, 'NOMOR_SURAT2');
                    }
                }
            }
            return false;
        }

        return str_contains((string)$template, 'NOMOR_SURAT2');
    }

    /**
     * Get shared next nomor urut for rekom across all perijinan with the same kode_perijinan.
     */
    public function getSharedNextNomorRekom(): int
    {
        if (!empty($this->kode_perijinan)) {
            $max = static::where('kode_perijinan', $this->kode_perijinan)->max('next_nomor_rekom');
            return max((int)($max ?? 1), 1);
        }
        return max((int)($this->next_nomor_rekom ?? 1), 1);
    }

    /**
     * Get shared next nomor urut for izin across all perijinan with the same kode_perijinan.
     */
    public function getSharedNextNomorIzin(): int
    {
        if (!empty($this->kode_perijinan)) {
            $max = static::where('kode_perijinan', $this->kode_perijinan)->max('next_nomor_izin');
            return max((int)($max ?? 1), 1);
        }
        return max((int)($this->next_nomor_izin ?? 1), 1);
    }

    /**
     * Increment shared nomor urut across all perijinan with the same kode_perijinan.
     */
    public function incrementSharedNomor(string $type, int $amount = 1): void
    {
        $column = ($type === 'rekom') ? 'next_nomor_rekom' : 'next_nomor_izin';

        if (!empty($this->kode_perijinan)) {
            $currentMax = (int)(static::where('kode_perijinan', $this->kode_perijinan)->max($column) ?? 1);
            $newNumber = $currentMax + $amount;
            static::where('kode_perijinan', $this->kode_perijinan)->update([$column => $newNumber]);
            $this->$column = $newNumber;
        } else {
            $this->increment($column, $amount);
        }
    }

    /**
     * Synchronize a specific nomor urut across all perijinan with the same kode_perijinan.
     */
    public function syncSharedNomor(string $type, int $number): void
    {
        $column = ($type === 'rekom') ? 'next_nomor_rekom' : 'next_nomor_izin';

        if (!empty($this->kode_perijinan)) {
            static::where('kode_perijinan', $this->kode_perijinan)->update([$column => $number]);
            $this->$column = $number;
        } else {
            $this->update([$column => $number]);
        }
    }
}
