<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record(?User $user, string $action, $model = null, array $new = [], array $old = [])
    {
        static::create(['user_id' => $user?->id, 'action' => $action, 'auditable_type' => $model ? $model::class : null, 'auditable_id' => $model?->id, 'old_values' => $old, 'new_values' => $new, 'ip_address' => request()->ip(), 'user_agent' => request()->userAgent()]);
    }
}
