<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'kost_id',
        'owner_id',
        'user_id',
        'move_in_date',
        'tenant_phone',
        'tenant_notes',
        'owner_notes',
        'status',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'tenant_phone' => 'encrypted',
        'tenant_notes' => 'encrypted',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeOwnedBy($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('user_id', $tenantId);
    }

    public function scopeStatus($query, ?string $status)
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('move_in_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('move_in_date', '<=', $to);
        }

        return $query;
    }
}
