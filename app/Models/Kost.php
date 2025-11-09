<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Kost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'price_per_month',
        'facilities',
        'description',
        'photo_path',
        'map_embed',
    ];

    protected $casts = [
        'facilities' => 'array',
        'price_per_month' => 'decimal:2',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeOwnedBy($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function ($sub) use ($keyword) {
            $sub->where('name', 'ilike', "%{$keyword}%")
                ->orWhere('address', 'ilike', "%{$keyword}%");
        });
    }

    public function scopePriceBetween($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price_per_month', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price_per_month', '<=', $max);
        }

        return $query;
    }

    public function scopeHasFacilities($query, array $facilities = [])
    {
        foreach ($facilities as $facility) {
            $query->whereJsonContains('facilities', $facility);
        }

        return $query;
    }

    public function photos()
    {
        return $this->hasMany(KostPhoto::class)->orderBy('position')->orderBy('id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->relationLoaded('photos')
            ? $this->photos->first()
            : $this->photos()->first();

        if ($photo) {
            return $photo->url;
        }

        if (! $this->photo_path) {
            return null;
        }

        return self::storagePublicUrl($this->photo_path);
    }

    public static function storagePublicUrl(string $path): string
    {
        if (app()->runningInConsole()) {
            return Storage::disk('public')->url($path);
        }

        if ($request = request()) {
            $relative = 'storage/'.ltrim($path, '/');

            return $request->getSchemeAndHttpHost().'/'.$relative;
        }

        return Storage::disk('public')->url($path);
    }
}
