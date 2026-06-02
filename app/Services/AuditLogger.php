<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\TaxCase;
use Illuminate\Http\Request;

class AuditLogger
{
    public function __construct(private readonly Request $request) {}

    public function record(string $action, ?TaxCase $case = null, array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => $this->request->user()?->id,
            'tax_case_id' => $case?->id,
            'action' => $action,
            'metadata' => $metadata ?: null,
            'ip_address' => $this->request->ip(),
            'user_agent' => (string) $this->request->userAgent(),
        ]);
    }
}
