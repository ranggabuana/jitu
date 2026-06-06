<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPerijinanReturnLog extends Model
{
    protected $table = 'data_perijinan_return_logs';

    protected $fillable = [
        'data_perijinan_id',
        'from_user_id',
        'from_role_label',
        'to_role_label',
        'catatan',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(DataPerijinan::class, 'data_perijinan_id');
    }
}
