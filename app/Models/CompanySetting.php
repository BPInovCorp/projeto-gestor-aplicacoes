<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'name',
        'vat_number',
        'address',
        'postal_code',
        'city',
        'logo_path',
    ];
}