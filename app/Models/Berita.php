<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'gambar',
        'user_id',
        'status',
        'is_featured',
        'views',
    ];

    protected $casts = [
        'views' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul);
            }
        });

        static::updating(function ($berita) {
            if ($berita->isDirty('judul') && empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul);
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
     * Get the user who created the news.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active news.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope a query to only include inactive news.
     */
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'tidak_aktif');
    }

    /**
     * Scope a query to only include featured news.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include slider news.
     */
    public function scopeSlider($query)
    {
        return $query->where('is_featured', true);
    }
}
