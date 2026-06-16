<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data_perijinan_id',
        'document_type',
        'status',
        'error_message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataPerijinan()
    {
        return $this->belongsTo(DataPerijinan::class, 'data_perijinan_id');
    }
}
