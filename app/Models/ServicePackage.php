<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'case_type',
        'features',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
