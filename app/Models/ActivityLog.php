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
    public static function log(string $description, $subject = null, string $event = null, array $properties = [], string $logName = 'default', $userId = null)
    {
        try {
            return static::create([
                'user_id' => $userId ?? auth()->id(), // Use provided userId or fallback to auth
                'log_name' => $logName,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->id : null,
                'event' => $event,
                'properties' => !empty($properties) ? $properties : null,
                'ip_address' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan operasi utama
            \Log::error('Failed to create activity log: ' . $e->getMessage());
            return null;
        }
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

    /**
     * Get human-readable field changes.
     */
    public function getFormattedChangesAttribute(): array
    {
        if (!$this->properties || !is_array($this->properties)) {
            return [];
        }

        $old = $this->properties['old'] ?? [];
        $new = $this->properties['new'] ?? [];

        // Handle case where properties has 'data' key (nested structure)
        if (isset($this->properties['data']) && is_array($this->properties['data'])) {
            $data = $this->properties['data'];
            $excludeFields = ['updated_at', 'created_at', 'password', 'remember_token', 'email_verified_at', '_token'];
            $fieldLabels = $this->getFieldLabels();
            $changes = [];

            foreach ($data as $field => $value) {
                if (in_array($field, $excludeFields)) {
                    continue;
                }

                if (is_array($value)) {
                    continue;
                }

                $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));
                $changes[] = [
                    'field' => $field,
                    'label' => $label,
                    'old' => '-',
                    'new' => $this->formatValue($field, $value),
                    'old_raw' => null,
                    'new_raw' => $value,
                ];
            }

            return $changes;
        }

        // Handle case where properties is a flat object (e.g., created event)
        if (empty($old) && empty($new)) {
            $excludeFields = ['updated_at', 'created_at', 'password', 'remember_token', 'email_verified_at', '_token'];
            $fieldLabels = $this->getFieldLabels();
            $changes = [];

            foreach ($this->properties as $field => $value) {
                if (in_array($field, $excludeFields)) {
                    continue;
                }

                if (is_array($value)) {
                    continue;
                }

                $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));
                $changes[] = [
                    'field' => $field,
                    'label' => $label,
                    'old' => '-',
                    'new' => $this->formatValue($field, $value),
                    'old_raw' => null,
                    'new_raw' => $value,
                ];
            }

            return $changes;
        }

        if (empty($old) && empty($new)) {
            return [];
        }

        $changes = [];
        $allFields = array_unique(array_merge(array_keys($old), array_keys($new)));

        // Fields to exclude from display
        $excludeFields = ['updated_at', 'created_at', 'password', 'remember_token', 'email_verified_at'];

        // Human-readable field labels
        $fieldLabels = $this->getFieldLabels();

        foreach ($allFields as $field) {
            if (in_array($field, $excludeFields)) {
                continue;
            }

            $oldValue = $old[$field] ?? null;
            $newValue = $new[$field] ?? null;

            // Skip if both values are the same
            if ($oldValue === $newValue) {
                continue;
            }

            $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));

            // Format boolean/null values
            $oldDisplay = $this->formatValue($field, $oldValue);
            $newDisplay = $this->formatValue($field, $newValue);

            $changes[] = [
                'field' => $field,
                'label' => $label,
                'old' => $oldDisplay,
                'new' => $newDisplay,
                'old_raw' => $oldValue,
                'new_raw' => $newValue,
            ];
        }

        return $changes;
    }

    /**
     * Get human-readable field labels.
     */
    private function getFieldLabels(): array
    {
        return [
            'nama_perijinan' => 'Nama Perizinan',
            'nama_lengkap' => 'Nama Lengkap',
            'no_registrasi' => 'No Registrasi',
            'status' => 'Status',
            'judul' => 'Judul',
            'konten' => 'Konten',
            'deskripsi' => 'Deskripsi',
            'gambar' => 'Gambar',
            'file_regulasi' => 'File Regulasi',
            'nama_regulasi' => 'Nama Regulasi',
            'nama' => 'Nama',
            'email' => 'Email',
            'telepon' => 'Telepon',
            'alamat' => 'Alamat',
            'jabatan' => 'Jabatan',
            'role' => 'Role',
            'is_featured' => 'Unggulan',
            'kategori_pengaduan_id' => 'Kategori Pengaduan',
            'isi_pengaduan' => 'Isi Pengaduan',
            'lokasi_pengaduan' => 'Lokasi Pengaduan',
            'catatan' => 'Catatan',
            'current_step' => 'Langkah Saat Ini',
            'catatan_perbaikan' => 'Catatan Perbaikan',
            'file_size' => 'Ukuran File',
            'file_type' => 'Tipe File',
            'slug' => 'Slug',
            'user_id' => 'User ID',
            '_token' => 'Token',
        ];
    }

    /**
     * Format value for display.
     */
    private function formatValue(string $field, $value): string
    {
        if ($value === null) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $strValue = (string) $value;

        // Format file_size (bytes) to human-readable
        if ($field === 'file_size' && is_numeric($value)) {
            $bytes = intval($value);
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }

        // Format file_type to readable label
        if ($field === 'file_type') {
            $typeLabels = [
                'application/pdf' => 'PDF',
                'application/msword' => 'Word (DOC)',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word (DOCX)',
                'application/vnd.ms-excel' => 'Excel (XLS)',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel (XLSX)',
                'image/jpeg' => 'Gambar JPEG',
                'image/png' => 'Gambar PNG',
                'image/jpg' => 'Gambar JPG',
            ];
            return $typeLabels[$strValue] ?? $strValue;
        }

        // Format file path to show only filename
        if ($field === 'file_regulasi' || $field === 'gambar') {
            return basename($strValue);
        }

        // Format yes/no for common boolean-like strings
        if (in_array($field, ['is_featured', 'is_active', 'aktif'])) {
            if ($value === true || $value === 1 || $value === '1' || $value === 'ya' || $value === 'aktif') {
                return 'Ya';
            }
            return 'Tidak';
        }

        // Truncate long text
        if (strlen($strValue) > 100) {
            return substr($strValue, 0, 100) . '...';
        }

        return $strValue;
    }

    /**
     * Get human-readable event label.
     */
    public function getEventLabelAttribute(): string
    {
        $labels = [
            'created' => 'Data Dibuat',
            'updated' => 'Data Diubah',
            'deleted' => 'Data Dihapus',
            'login' => 'Masuk Sistem',
            'logout' => 'Keluar Sistem',
            'viewed' => 'Data Dilihat',
            'exported' => 'Data Diekspor',
            'imported' => 'Data Diimpor',
            'restored' => 'Data Dipulihkan',
        ];

        return $labels[$this->event] ?? ucfirst($this->event ?? 'Aktivitas');
    }

    /**
     * Get human-readable subject info.
     */
    public function getSubjectLabelAttribute(): string
    {
        if (!$this->subject_type || !$this->subject_id) {
            return 'Sistem';
        }

        $modelName = class_exists($this->subject_type) ? class_basename($this->subject_type) : $this->subject_type;

        // Try to get additional info from properties
        if ($this->properties && is_array($this->properties)) {
            $subjectInfo = $this->properties['subject'] ?? null;
            if ($subjectInfo && is_array($subjectInfo)) {
                $label = $subjectInfo['label'] ?? null;
                if ($label) {
                    return $label;
                }
            }
        }

        // Map model names to Indonesian labels
        $modelLabels = [
            'Berita' => 'Berita',
            'Regulasi' => 'Regulasi',
            'Perijinan' => 'Perizinan',
            'DataPerijinan' => 'Data Perizinan',
            'User' => 'Pengguna',
            'Pengaduan' => 'Pengaduan',
            'KategoriPengaduan' => 'Kategori Pengaduan',
            'Settings' => 'Pengaturan',
        ];

        $label = $modelLabels[$modelName] ?? $modelName;

        return $label . ' #' . $this->subject_id;
    }

    /**
     * Get human-readable log name label.
     */
    public function getLogNameLabelAttribute(): string
    {
        $labels = [
            'default' => 'Aktivitas Umum',
            'authentication' => 'Autentikasi',
            'perijinan' => 'Perizinan',
            'berita' => 'Berita',
            'regulasi' => 'Regulasi',
            'pengaduan' => 'Pengaduan',
            'user' => 'Pengguna',
            'settings' => 'Pengaturan',
        ];

        return $labels[$this->log_name] ?? ucfirst($this->log_name ?? 'Aktivitas');
    }
}
