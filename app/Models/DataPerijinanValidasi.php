<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPerijinanValidasi extends Model
{
    protected $table = 'data_perijinan_validasi';

    protected $fillable = [
        'data_perijinan_id',
        'validation_flow_id',
        'user_id',
        'status',
        'catatan',
        'validated_at',
        'order',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /**
     * Get the application.
     */
    public function dataPerijinan(): BelongsTo
    {
        return $this->belongsTo(DataPerijinan::class, 'data_perijinan_id');
    }

    /**
     * Get the validation flow.
     */
    public function validationFlow(): BelongsTo
    {
        return $this->belongsTo(PerijinanValidationFlow::class, 'validation_flow_id');
    }

    /**
     * Get the validator user.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if validation is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if validation is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if validation needs revision.
     */
    public function needsRevision(): bool
    {
        return $this->status === 'revision';
    }

    /**
     * Check if validation is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Perbaikan',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'bg-gray-100 text-gray-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'revision' => 'bg-orange-100 text-orange-800',
        ];
        
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
