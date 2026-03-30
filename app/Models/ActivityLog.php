<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent subject model.
     */
    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity.
     */
    public static function log(string $description, $subject = null, string $event = null, array $properties = [], string $logName = 'default')
    {
        return static::create([
            'user_id' => auth()->id(),
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'event' => $event,
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get icon based on event type.
     */
    public function getEventIconAttribute(): string
    {
        $icons = [
            'created' => 'mdi-plus-circle',
            'updated' => 'mdi-pencil',
            'deleted' => 'mdi-delete',
            'login' => 'mdi-login',
            'logout' => 'mdi-logout',
            'viewed' => 'mdi-eye',
            'exported' => 'mdi-download',
            'imported' => 'mdi-upload',
            'restored' => 'mdi-restore',
        ];

        return $icons[$this->event] ?? 'mdi-information';
    }

    /**
     * Get color based on event type.
     */
    public function getEventColorAttribute(): string
    {
        $colors = [
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'login' => 'purple',
            'logout' => 'gray',
            'viewed' => 'indigo',
            'exported' => 'teal',
            'imported' => 'orange',
            'restored' => 'emerald',
        ];

        return $colors[$this->event] ?? 'gray';
    }
}
