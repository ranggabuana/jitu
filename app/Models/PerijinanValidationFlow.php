<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PerijinanValidationFlow extends Model
{
    use HasFactory;

    protected $table = 'perijinan_validation_flows';

    protected $fillable = [
        'perijinan_id',
        'role',
        'assigned_user_id',
        'order',
        'is_active',
        'description',
        'sla_hours',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'sla_hours' => 'integer',
    ];

    /**
     * Get the perijinan that owns the validation flow.
     */
    public function perijinan(): BelongsTo
    {
        return $this->belongsTo(Perijinan::class);
    }

    /**
     * Get the assigned user for this validation flow.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get users by role for dropdown.
     */
    public static function getUsersByRole(string $role): array
    {
        $query = User::where('role', $role);
        
        // For OPD roles, filter by active users only
        if (in_array($role, ['operator_opd', 'kepala_opd'])) {
            $query->where('status', 'aktif');
        }
        
        return $query->orderBy('name')->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Check if role requires user assignment.
     */
    public static function requiresUserAssignment(string $role): bool
    {
        return in_array($role, ['operator_opd', 'kepala_opd']);
    }

    /**
     * Get the role label.
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'fo' => 'Front Office',
            'bo' => 'Back Office',
            'operator_opd' => 'Operator OPD',
            'kepala_opd' => 'Kepala OPD',
            'verifikator' => 'Verifikator',
            'kadin' => 'Kadin',
            'admin' => 'Admin',
        ];
        return $labels[$this->role] ?? $this->role;
    }

    /**
     * Get the icon for the role.
     */
    public function getRoleIconAttribute(): string
    {
        $icons = [
            'fo' => 'mdi-account-tie',
            'bo' => 'mdi-briefcase',
            'operator_opd' => 'mdi-office-building',
            'kepala_opd' => 'mdi-account-tie-outline',
            'verifikator' => 'mdi-clipboard-check',
            'kadin' => 'mdi-handshake',
            'admin' => 'mdi-shield-account',
        ];
        return $icons[$this->role] ?? 'mdi-account';
    }

    /**
     * Get the color for the role.
     */
    public function getRoleColorAttribute(): string
    {
        $colors = [
            'fo' => 'blue',
            'bo' => 'purple',
            'operator_opd' => 'indigo',
            'kepala_opd' => 'violet',
            'verifikator' => 'emerald',
            'kadin' => 'amber',
            'admin' => 'slate',
        ];
        return $colors[$this->role] ?? 'gray';
    }

    /**
     * Get all available roles for validation flow.
     */
    public static function getAvailableRoles(): array
    {
        return [
            'fo' => 'Front Office',
            'bo' => 'Back Office',
            'operator_opd' => 'Operator OPD',
            'kepala_opd' => 'Kepala OPD',
            'verifikator' => 'Verifikator',
            'kadin' => 'Kadin',
        ];
    }
}
