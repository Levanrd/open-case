<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'registered_name',
        'trade_name',
        'tin_encrypted',
        'tin_last_four',
        'rdo_code',
        'business_type',
        'taxpayer_type',
        'registered_address',
        'registration_date',
        'business_activity',
        'line_of_business',
        'business_status',
        'last_year_of_operation',
        'still_operating',
        'primary_goal',
    ];

    protected $hidden = [
        'tin_encrypted',
    ];

    protected $appends = [
        'masked_tin',
    ];

    protected function casts(): array
    {
        return [
            'tin_encrypted' => 'encrypted',
            'registration_date' => 'date',
            'still_operating' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(TaxCase::class);
    }

    public function getMaskedTinAttribute(): ?string
    {
        return $this->tin_last_four ? '***-***-'.$this->tin_last_four : null;
    }
}
