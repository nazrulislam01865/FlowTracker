<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderWorkflowEmailHandoffImplementationTest extends TestCase
{
    public function test_order_workflow_handoffs_use_team_members_central_email_and_exact_preview(): void
    {
        $service = file_get_contents(app_path('Services/Orders/OrderWorkflowEmailService.php'));
        $actions = file_get_contents(app_path('Services/OrderWorkflowActionService.php'));
        $modal = file_get_contents(resource_path('views/components/jobs/order-detail/workflow-action-modal.blade.php'));
        $preview = file_get_contents(resource_path('views/components/email/handoff-preview.blade.php'));
        $email = file_get_contents(resource_path('views/emails/orders/workflow-handoff.blade.php'));

        $this->assertStringContainsString('EmailService $email', $service);
        $this->assertStringContainsString('EmailMessage::storageAttachment', $service);
        $this->assertStringContainsString('NEW_SEND_PO_ARTWORK', $service);
        $this->assertStringContainsString('ART_SEND_ORDER_TEAM', $service);
        $this->assertStringContainsString("'job.purchase_order_emailed_to_artwork_team'", $service);
        $this->assertStringContainsString("'job.artwork_emailed_to_order_team'", $service);

        // Recipient rules are explicit and can return multiple people.
        // Artwork -> Order Team uses Administration > Users & role assignments.
        $this->assertStringContainsString('orderTeamRoleMembers($job)', $service);
        $this->assertStringContainsString("whereHas('roles'", $service);
        $this->assertStringContainsString("=== 'orderteam'", $service);
        $this->assertStringContainsString('workspaceMemberships', $service);

        // Order Team delivery is client/business-unit scoped. NEP Orders go to
        // NEP + Both; IID Orders go to IID + Both. This uses the Business unit
        // stored by Users & role assignments on workspace_memberships.
        $this->assertStringContainsString("whereIn('business_unit', [\$businessUnit, 'both'])", $service);
        $this->assertStringContainsString("orWhereNull('business_unit')", $service);
        $this->assertStringContainsString("if (\$code === 'iid') return 'iid';", $service);
        $this->assertStringContainsString("if (\$code === 'nep') return 'nep';", $service);
        $this->assertStringContainsString("Order Team role + '.strtoupper(\$businessUnit).' business unit", $service);
        $this->assertStringContainsString("'business_unit' => \$key === self::ARTWORK_HANDOFF", $service);

        // Purchase Order -> Artwork Team uses every active assignee from this
        // Order's Artwork phase, not one hard-coded user/email.
        $this->assertStringContainsString('artworkPhaseAssignees($job)', $service);
        $this->assertStringContainsString("whereIn('workflow_phase_id', \$artworkPhaseIds->all())", $service);
        $this->assertStringContainsString("whereNotNull('assignee_id')", $service);
        $this->assertStringContainsString("All assignees in this Order's Artwork phase", $service);

        // Preview and send share the exact same email Blade/view data.
        $this->assertStringContainsString("view('emails.orders.workflow-handoff', \$viewData)->render()", $service);
        $this->assertStringContainsString("'html' => \$previewHtml", $service);
        $this->assertStringContainsString("'delivery' => \$emailServiceEnabled ? \$this->deliveryLabel()", $service);
        $this->assertStringContainsString('private function sourceDocuments(', $service);
        $this->assertStringContainsString("where('version', \$latestVersion)", $service);
        $this->assertStringContainsString('attachments: $attachments', $service);
        $this->assertStringContainsString("'from_address' => \$this->senderAddress()", $service);

        $this->assertStringContainsString("'variant' => 'purchase_order_email'", $actions);
        $this->assertStringContainsString('OrderWorkflowEmailService::class)->send', $actions);
        $this->assertStringNotContainsString("['NEW_SEND_PO_ARTWORK', 'PROD_START'", $actions);

        $this->assertStringContainsString('preview($task, auth()->user())', $modal);
        $this->assertStringContainsString('<x-email.handoff-preview', $modal);
        $this->assertStringContainsString('emptyRecipientText=', $modal);
        $this->assertStringContainsString('orderWorkflowActionEmail', $modal);

        $this->assertStringContainsString('Exact email that will be sent', $preview);
        $this->assertStringContainsString('ft-order-email-recipient-chip', $preview);
        $this->assertStringContainsString('Recipient rule', $preview);
        $this->assertStringContainsString('empty_recipient_message', $preview);
        $this->assertStringContainsString('srcdoc="{{ $html }}"', $preview);
        $this->assertStringContainsString('Message body preview', $preview);

        $this->assertStringContainsString('Purchase Order ready for Artwork', $email);
        $this->assertStringContainsString('Artwork ready for Order Team', $email);
        $this->assertStringContainsString('$brand', $email);
    }
}
