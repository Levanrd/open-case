<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseResult extends Model
{
    protected $fillable = [
        'tax_case_id',
        'result_type',
        'risk_level',
        'risk_score',
        'generated_summary',
        'recommendations',
        'disclaimer_accepted',
    ];

    protected function casts(): array
    {
        return [
            'recommendations' => 'array',
            'disclaimer_accepted' => 'boolean',
        ];
    }

    public function taxCase(): BelongsTo
    {
        return $this->belongsTo(TaxCase::class);
    }
}
