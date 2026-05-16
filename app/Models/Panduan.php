<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_panduan',
        'slug',
        'file',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
