<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'entity_id',
        'contact_role_id',
        'first_name',
        'last_name',
        'phone',
        'mobile',
        'email',
        'rgpd_consent',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'rgpd_consent' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Contact $contact) {
            if (empty($contact->number)) {
                $contact->number = ((int) static::query()->max('number')) + 1;
            }
        });
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(ContactRole::class, 'contact_role_id');
    }
}