<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_client',
        'is_supplier',
        'nif',
        'nif_hash',
        'address',
        'city',
        'postal_code',
        'country_id',
        'phone',
        'mobile',
        'website',
        'email',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_client' => 'boolean',
            'is_supplier' => 'boolean',
            'is_active' => 'boolean',
            'nif' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Entity $entity) {
            $entity->nif_hash = $entity->nif ? hash('sha256', $entity->nif) : null;
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function scopeClients(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_client', true);
    }

    public function scopeSuppliers(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_supplier', true);
    }
}