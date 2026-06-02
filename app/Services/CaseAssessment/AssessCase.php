<?php

namespace App\Services\CaseAssessment;

use App\Models\TaxCase;

class AssessCase
{
    public function handle(TaxCase $case): array
    {
        $case->loadMissing('answers', 'business');

        $answers = $case->answers
            ->mapWithKeys(fn ($answer) => [$answer->question_key => $answer->answer_value])
            ->all();

        $riskScore = 0;
        $recommendations = [];
        $checklist = [
            [
                'title' => 'Review Certificate of Registration',
                'description' => 'Confirm registered tax types, RDO, registration date, and business classification.',
            ],
        ];

        if ($this->answer($answers, 'has_cor') === 'no') {
            $riskScore += 20;
            $recommendations[] = 'Upload your Certificate of Registration for accurate review.';
            $checklist[] = [
                'title' => 'Upload Certificate of Registration',
                'description' => 'The COR is needed to verify tax types and filing obligations.',
            ];
        }

        if (
            $this->answer($answers, 'still_operating') === 'no'
            && $this->answer($answers, 'filed_closure') === 'no'
        ) {
            $riskScore += 35;
            $recommendations[] = 'Closure review is recommended before assuming filing duties have stopped.';
            $checklist[] = [
                'title' => 'Prepare closure requirements',
                'description' => 'Gather closure forms, receipts or invoice inventory, books of accounts, and RDO details.',
            ];
        }

        if ($this->answer($answers, 'received_bir_notice') === 'yes') {
            $riskScore += 40;
            $recommendations[] = 'Accountant review is strongly recommended.';
            $checklist[] = [
                'title' => 'Upload BIR notice',
                'description' => 'An accountant should review the notice date, requested forms, and stated deadlines.',
            ];
        }

        if ($this->answer($answers, 'has_unused_receipts') === 'yes') {
            $riskScore += 10;
            $recommendations[] = 'Unused receipts or invoices may need inventory review during closure.';
            $checklist[] = [
                'title' => 'Inventory unused receipts or invoices',
                'description' => 'List unused booklets or invoices and keep them available for review.',
            ];
        }

        if ($this->answer($answers, 'has_books') === 'no') {
            $riskScore += 10;
            $recommendations[] = 'Books of accounts status should be checked before closure or cleanup.';
        }

        $taxTypes = $answers['tax_types'] ?? [];
        $taxTypes = is_array($taxTypes) ? $taxTypes : [$taxTypes];

        if (in_array('2551Q', $taxTypes, true)) {
            $checklist[] = [
                'title' => 'Prepare percentage tax return records',
                'description' => 'Collect filed 2551Q returns or payment confirmations for the review period.',
            ];
        }

        if (in_array('1701Q', $taxTypes, true)) {
            $checklist[] = [
                'title' => 'Prepare quarterly income tax records',
                'description' => 'Collect 1701Q filings and payment confirmations for each relevant quarter.',
            ];
        }

        if (in_array('1701', $taxTypes, true)) {
            $checklist[] = [
                'title' => 'Prepare annual income tax records',
                'description' => 'Collect annual income tax returns and supporting schedules.',
            ];
        }

        if (in_array('0605', $taxTypes, true)) {
            $checklist[] = [
                'title' => 'Check annual registration fee records',
                'description' => 'Prepare Form 0605 or proof of payment where applicable.',
            ];
        }

        $lastFiledYear = (int) $this->answer($answers, 'last_filed_year', 0);
        if ($lastFiledYear > 0 && $lastFiledYear <= ((int) now()->year - 2)) {
            $riskScore += 25;
            $recommendations[] = 'Multiple years may need filing review before closure or cleanup.';
            $checklist[] = [
                'title' => 'List missing filing years',
                'description' => 'Create a year-by-year list of returns and payments available for review.',
            ];
        }

        $riskScore = min($riskScore, 100);
        $resultType = $this->resultType($riskScore, $case->case_type);

        return [
            'risk_score' => $riskScore,
            'risk_level' => $this->riskLevel($riskScore),
            'result_type' => $resultType,
            'generated_summary' => $this->summary($resultType, $riskScore),
            'recommendations' => $recommendations,
            'checklist' => $checklist,
        ];
    }

    private function resultType(int $riskScore, string $caseType): string
    {
        return match (true) {
            $riskScore >= 70 => 'accountant_required',
            $caseType === 'business_closure' && $riskScore >= 35 => 'closure_preparation_needed',
            $riskScore >= 45 => 'likely_open_case',
            $riskScore >= 25 => 'needs_review',
            default => 'low_risk',
        };
    }

    private function answer(array $answers, string $key, mixed $default = null): mixed
    {
        $value = $answers[$key] ?? $default;

        if (is_array($value) && array_is_list($value)) {
            return $value[0] ?? $default;
        }

        return $value;
    }

    private function riskLevel(int $riskScore): string
    {
        return match (true) {
            $riskScore >= 70 => 'High',
            $riskScore >= 45 => 'Elevated',
            $riskScore >= 25 => 'Moderate',
            default => 'Low',
        };
    }

    private function summary(string $resultType, int $riskScore): string
    {
        return match ($resultType) {
            'accountant_required' => "Your answers show high review priority with a risk score of {$riskScore}. A qualified accountant should review the case before any closure or cleanup decision.",
            'likely_open_case' => "Your answers suggest possible open-case exposure with a risk score of {$riskScore}. Review missing filings and notices before taking next steps.",
            'closure_preparation_needed' => "Your answers point to closure preparation needs with a risk score of {$riskScore}. Prepare documents and verify obligations before assuming the business is closed.",
            'needs_review' => "Your answers show items that need review with a risk score of {$riskScore}. Organizing documents now can reduce delay later.",
            default => "Your answers currently indicate lower review risk with a risk score of {$riskScore}. Keep the supporting documents organized for confirmation.",
        };
    }
}
