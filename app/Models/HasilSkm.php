<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasilSkm extends Model
{
    use HasFactory;

    protected $table = 'hasil_skm';

    protected $fillable = [
        'data_skm_id',
        'responden_nama',
        'responden_email',
        'nip',
        'jawaban',
        'saran',
        'ip_address',
        'user_id',
    ];

    protected $casts = [
        'jawaban' => 'string',
    ];

    /**
     * Get the question for this response.
     */
    public function dataSkm()
    {
        return $this->belongsTo(DataSkm::class);
    }

    /**
     * Get the user who submitted this response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the label for the answer.
     */
    public function getJawabanLabelAttribute()
    {
        return DataSkm::getSkalaLabel($this->jawaban);
    }

    /**
     * Get the score for this answer.
     */
    public function getScoreAttribute()
    {
        return (int) $this->jawaban;
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by question.
     */
    public function scopeByQuestion($query, $dataSkmId)
    {
        return $query->where('data_skm_id', $dataSkmId);
    }
}
