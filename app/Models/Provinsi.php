<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = ['code', 'name'];

    public function kabupatens()
    {
        return $this->hasMany(Kabupaten::class);
    }
}
