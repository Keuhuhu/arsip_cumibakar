<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // Only created_at

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Log helper
    public static function log(string $action, string $description, ?Model $model = null, array $properties = []): void
    {
        $user = auth()->user();

        static::create([
            'user_id'     => $user?->id,
            'action'      => $action,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->id,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'properties'  => $properties,
        ]);
    }

    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'upload', 'create' => 'badge-success',
            'download'         => 'badge-info',
            'delete'           => 'badge-danger',
            'edit', 'update'   => 'badge-warning',
            'login'            => 'badge-primary',
            'approve'          => 'badge-success',
            default            => 'badge-secondary',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'upload'   => 'Unggah',
            'create'   => 'Buat',
            'download' => 'Unduh',
            'delete'   => 'Hapus',
            'edit'     => 'Edit',
            'update'   => 'Perbarui',
            'login'    => 'Login',
            'logout'   => 'Logout',
            'approve'  => 'Setujui',
            'restore'  => 'Restore',
            'backup'   => 'Backup',
            default    => ucfirst($this->action),
        };
    }
}