<?php

namespace App\Services\CaseAssessment;

use App\Models\TaxCase;

class AssessCase
{
    public function handle(TaxCase $case): array
    {
        $answers = $case->answers()->pluck('answer_value', 'question_key');

        $riskScore = 0;
        $recommendations = [];

        if (($answers['has_cor'] ?? null) === 'no') {
            $riskScore += 20;
            $recommendations[] = 'Upload your Certificate of Registration for accurate review.';
        }

        if (
            ($answers['stopped_operating'] ?? null) === 'yes'
            && ($answers['filed_closure'] ?? null) === 'no'
        ) {
            $riskScore += 35;
            $recommendations[] = 'Business closure review is recommended.';
        }

        if (($answers['received_bir_notice'] ?? null) === 'yes') {
            $riskScore += 40;
            $recommendations[] = 'Accountant review is strongly recommended.';
        }

        return [
            'risk_score' => min($riskScore, 100),
            'result_type' => $this->resultType($riskScore),
            'recommendations' => $recommendations,
        ];
    }

    private function resultType(int $riskScore): string
    {
        return match (true) {
            $riskScore >= 70 => 'accountant_required',
            $riskScore >= 45 => 'likely_open_case',
            $riskScore >= 25 => 'needs_review',
            default => 'low_risk',
        };
    }
}
