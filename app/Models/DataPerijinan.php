<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPerijinan extends Model
{
    use HasFactory;

    protected $table = 'data_perijinan';

    protected $fillable = [
        'no_registrasi',
        'user_id',
        'perijinan_id',
        'status',
        'form_data',
        'form_files',
        'data_pemohon',
        'catatan_perbaikan',
        'catatan_reject',
        'current_step',
        'submitted_at',
        'approved_at',
        'completed_at',
        'rejected_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'form_files' => 'array',
        'data_pemohon' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($data) {
            if (empty($data->no_registrasi)) {
                $data->no_registrasi = static::generateNoRegistrasi();
            }
        });
    }

    /**
     * Generate unique registration number.
     * Format: REG-YYYYMMDD-XXXXX (XXXXX = random 5 digits)
     */
    private static function generateNoRegistrasi()
    {
        $date = now()->format('Ymd');
        $random = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        
        $noRegistrasi = 'REG-' . $date . '-' . $random;
        
        // Ensure uniqueness
        while (static::where('no_registrasi', $noRegistrasi)->exists()) {
            $random = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            $noRegistrasi = 'REG-' . $date . '-' . $random;
        }
        
        return $noRegistrasi;
    }

    /**
     * Get the user who submitted the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the perijinan type.
     */
    public function perijinan(): BelongsTo
    {
        return $this->belongsTo(Perijinan::class);
    }

    /**
     * Get all validation records for this application.
     */
    public function validasiRecords(): HasMany
    {
        return $this->hasMany(DataPerijinanValidasi::class, 'data_perijinan_id')->orderBy('order');
    }

    /**
     * Get pending validation records.
     */
    public function pendingValidasi(): HasMany
    {
        return $this->hasMany(DataPerijinanValidasi::class, 'data_perijinan_id')->where('status', 'pending');
    }

    /**
     * Get current validation step.
     */
    public function currentValidasi()
    {
        return $this->hasMany(DataPerijinanValidasi::class, 'data_perijinan_id')
            ->where('order', $this->current_step)
            ->first();
    }

    /**
     * Get completed validation records.
     */
    public function completedValidasi(): HasMany
    {
        return $this->hasMany(DataPerijinanValidasi::class, 'data_perijinan_id')->where('status', 'approved');
    }

    /**
     * Check if application is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application needs revision.
     */
    public function needsRevision(): bool
    {
        return $this->status === 'perbaikan';
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        $totalSteps = $this->perijinan->activeValidationFlows()->count();
        if ($totalSteps === 0) return 0;
        
        $completedSteps = $this->completedValidasi()->count();
        return round(($completedSteps / $totalSteps) * 100, 2);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'in_progress' => 'Dalam Proses',
            'perbaikan' => 'Perlu Perbaikan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'perbaikan' => 'bg-orange-100 text-orange-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];
        
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
