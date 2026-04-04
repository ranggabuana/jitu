<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $fillable = ['kecamatan_id', 'code', 'name', 'postal_code'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
