<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'no_pengaduan',
        'nama',
        'email',
        'no_hp',
        'kategori',
        'isi_pengaduan',
        'lampiran',
        'file_type',
        'file_size',
        'status',
        'respon',
        'user_id',
        'tanggal_pengaduan',
        'tanggal_respon',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'tanggal_pengaduan' => 'datetime',
        'tanggal_respon' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengaduan) {
            if (empty($pengaduan->no_pengaduan)) {
                $pengaduan->no_pengaduan = static::generateNoPengaduan();
            }
        });
    }

    /**
     * Generate unique complaint number.
     * Format: PENG-YYYYMMDD-XXXXX
     */
    private static function generateNoPengaduan()
    {
        $date = now()->format('Ymd');
        $prefix = 'PENG-' . $date . '-';
        
        // Get the last complaint number for today
        $lastPengaduan = static::where('no_pengaduan', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPengaduan) {
            $lastNumber = (int) substr($lastPengaduan->no_pengaduan, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Get the user who handled the complaint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending complaints.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in-progress complaints.
     */
    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    /**
     * Scope a query to only include completed complaints.
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Scope a query to only include rejected complaints.
     */
    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Get file size in human readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'yellow',
            'proses' => 'blue',
            'selesai' => 'green',
            'ditolak' => 'red',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get all categories.
     */
    public static function getCategories(): array
    {
        return [
            'pelayanan' => 'Pelayanan',
            'sarana' => 'Sarana & Prasarana',
            'pegawai' => 'Kepegawaian',
            'produk_hukum' => 'Produk Hukum',
            'lainnya' => 'Lainnya',
        ];
    }

    /**
     * Get all statuses.
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    }
}
