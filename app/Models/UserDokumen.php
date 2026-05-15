<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'master_dokumen_id',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masterDokumen()
    {
        return $this->belongsTo(MasterDokumenPemohon::class, 'master_dokumen_id');
    }
}