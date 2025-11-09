<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KostPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'kost_id',
        'path',
        'position',
    ];

    protected $appends = [
        'url',
    ];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function getUrlAttribute(): string
    {
        return Kost::storagePublicUrl($this->path);
    }

    protected static function booted(): void
    {
        static::deleted(function (KostPhoto $photo) {
            if (! $photo->path) {
                return;
            }

            Storage::disk('public')->delete($photo->path);
        });
    }
}

