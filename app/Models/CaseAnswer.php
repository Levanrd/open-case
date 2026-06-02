<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseAnswer extends Model
{
    protected $fillable = [
        'tax_case_id',
        'question_key',
        'answer_value',
    ];

    protected function casts(): array
    {
        return [
            'answer_value' => 'array',
        ];
    }

    public function taxCase(): BelongsTo
    {
        return $this->belongsTo(TaxCase::class);
    }
}
