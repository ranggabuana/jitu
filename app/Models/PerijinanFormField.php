<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerijinanFormField extends Model
{
    protected $table = 'perijinan_form_fields';

    protected $fillable = [
        'perijinan_id',
        'label',
        'name',
        'type',
        'is_required',
        'placeholder',
        'help_text',
        'options',
        'order',
        'is_active',
        'file_types',
        'max_file_size',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
    ];

    /**
     * Get the perijinan that owns the form field.
     */
    public function perijinan(): BelongsTo
    {
        return $this->belongsTo(Perijinan::class);
    }
}
