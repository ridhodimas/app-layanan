<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DtsenPurpose extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'max_decile',
        'validity_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_decile' => 'integer',
            'validity_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
