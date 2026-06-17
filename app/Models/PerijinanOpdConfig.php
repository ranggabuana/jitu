<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerijinanOpdConfig extends Model
{
    protected $table = 'perijinan_opd_configs';

    protected $fillable = [
        'perijinan_id',
        'opd_id',
        'template_surat_rekom',
        'keterangan_rekom',
        'next_nomor_rekom',
    ];

    /**
     * Get the perijinan that owns the config.
     */
    public function perijinan(): BelongsTo
    {
        return $this->belongsTo(Perijinan::class);
    }

    /**
     * Get the OPD that owns the config.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }
}
