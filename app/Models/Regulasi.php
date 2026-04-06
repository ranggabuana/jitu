<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Regulasi extends Model
{
    use HasFactory;

    protected $table = 'regulasi';

    protected $fillable = [
        'nama_regulasi',
        'slug',
        'file_regulasi',
        'file_type',
        'file_size',
        'deskripsi',
        'status',
        'user_id',
        'urutan',
        'jenis_regulasi_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($regulasi) {
            if (empty($regulasi->slug)) {
                $regulasi->slug = static::generateUniqueSlug($regulasi->nama_regulasi);
            }
        });

        static::updating(function ($regulasi) {
            if ($regulasi->isDirty('nama_regulasi') && empty($regulasi->slug)) {
                $regulasi->slug = static::generateUniqueSlug($regulasi->nama_regulasi);
            }
        });
    }

    /**
     * Generate unique slug from title.
     */
    private static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the user who created the regulasi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the jenis regulasi.
     */
    public function jenisRegulasi(): BelongsTo
    {
        return $this->belongsTo(JenisRegulasi::class);
    }

    /**
     * Scope a query to only include active regulasi.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope a query to only include inactive regulasi.
     */
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'tidak_aktif');
    }

    /**
     * Get file size in human readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
