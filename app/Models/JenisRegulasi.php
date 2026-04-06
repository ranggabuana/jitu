<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JenisRegulasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_regulasi';

    protected $fillable = [
        'nama_jenis',
        'slug',
        'deskripsi',
        'status',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jenis) {
            if (empty($jenis->slug)) {
                $jenis->slug = static::generateUniqueSlug($jenis->nama_jenis);
            }
        });

        static::updating(function ($jenis) {
            if ($jenis->isDirty('nama_jenis') && empty($jenis->slug)) {
                $jenis->slug = static::generateUniqueSlug($jenis->nama_jenis);
            }
        });
    }

    /**
     * Generate unique slug.
     */
    private static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get regulasi items for this jenis.
     */
    public function regulasi(): HasMany
    {
        return $this->hasMany(Regulasi::class);
    }

    /**
     * Scope active items.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
