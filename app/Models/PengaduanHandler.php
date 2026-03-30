<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanHandler extends Model
{
    protected $table = 'pengaduan_handlers';

    protected $fillable = [
        'user_id',
        'assigned_by',
        'assigned_at',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that handles pengaduan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who assigned this handler.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if user can access pengaduan menu.
     */
    public static function canAccessPengaduan($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }
        
        $user = User::find($userId);
        
        // Admin can always access
        if ($user && $user->role === 'admin') {
            return true;
        }
        
        // Check if user is assigned as handler
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all active handlers.
     */
    public static function getActiveHandlers()
    {
        return static::where('is_active', true)
            ->with(['user', 'assignedBy'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    /**
     * Assign user as handler.
     */
    public static function assignHandler($userId, $assignedBy)
    {
        return static::updateOrCreate(
            ['user_id' => $userId],
            [
                'assigned_by' => $assignedBy,
                'assigned_at' => now(),
                'is_active' => true,
            ]
        );
    }

    /**
     * Remove user as handler.
     */
    public static function removeHandler($userId)
    {
        return static::where('user_id', $userId)->delete();
    }
}
