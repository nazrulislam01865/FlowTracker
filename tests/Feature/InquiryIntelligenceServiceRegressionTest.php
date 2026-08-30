<?php

namespace Tests\Feature;

use Tests\TestCase;

class InquiryIntelligenceServiceRegressionTest extends TestCase
{
    public function test_portfolio_evidence_kpi_uses_the_calculated_submission_evidence_count(): void
    {
        $service = file_get_contents(app_path('Services/InquiryIntelligenceService.php'));

        $this->assertStringContainsString('$submissionWithEvidence = $submissionTasks', $service);
        $this->assertStringContainsString("'evidenced' => \$submissionWithEvidence", $service);
        $this->assertStringNotContainsString('$submissionWithFiles', $service);
    }
}
