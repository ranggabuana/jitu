<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDokumenPemohon extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_dokumen',
        'tipe_data_file',
        'jenis',
        'max_size',
    ];

    public function userDokumens()
    {
        return $this->hasMany(UserDokumen::class, 'master_dokumen_id');
    }
}