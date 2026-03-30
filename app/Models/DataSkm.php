<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataSkm extends Model
{
    use HasFactory;

    protected $table = 'data_skm';

    protected $fillable = [
        'pertanyaan',
        'bobot_max',
        'urutan',
        'status',
        'user_id',
    ];

    protected $casts = [
        'bobot_max' => 'integer',
        'urutan' => 'integer',
    ];

    /**
     * Get the user who created the question.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all responses for this question.
     */
    public function hasilSkm()
    {
        return $this->hasMany(HasilSkm::class);
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')->orderBy('urutan');
    }

    /**
     * Get label for skala value.
     */
    public static function getSkalaLabel($value)
    {
        $labels = [
            '1' => 'Kurang Baik',
            '2' => 'Cukup Baik',
            '3' => 'Baik',
            '4' => 'Sangat Baik',
        ];
        return $labels[$value] ?? $value;
    }

    /**
     * Get all skala labels.
     */
    public static function getSkalaLabels()
    {
        return [
            '1' => 'Kurang Baik',
            '2' => 'Cukup Baik',
            '3' => 'Baik',
            '4' => 'Sangat Baik',
        ];
    }
}
