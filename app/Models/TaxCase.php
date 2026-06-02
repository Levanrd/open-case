<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class TaxCase extends Model
{
    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATUS_PAYMENT_REVIEW = 'payment_review';

    public const STATUS_PAID = 'paid';

    public const STATUS_ASSIGNED = 'accountant_assigned';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_NEEDS_DOCUMENTS = 'needs_more_documents';

    public const STATUS_READY = 'ready_for_bir_processing';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'business_id',
        'assigned_accountant_id',
        'case_number',
        'case_type',
        'status',
        'risk_level',
        'summary',
        'final_recommendation',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TaxCase $case): void {
            $case->case_number ??= 'OC-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $case->submitted_at ??= now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function assignedAccountant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_accountant_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CaseAnswer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(CaseResult::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
