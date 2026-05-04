<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'warna',
        'icon',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function dokumens(): HasMany
    {
        return $this->hasMany(Dokumen::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function getDokumenCountAttribute(): int
    {
        return $this->dokumens()->count();
    }
}