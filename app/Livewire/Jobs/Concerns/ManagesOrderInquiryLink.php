<?php

namespace App\Livewire\Jobs\Concerns;

use App\Actions\Orders\LinkOrderInquiry;
use App\Actions\Orders\UnlinkOrderInquiry;
use App\Queries\Inquiries\InquiryDetailQuery;
use App\Queries\Orders\VisibleOrderQuery;
use App\Services\AccessControlService;
use Throwable;

/**
 * Phase 5 Order UI workflow extracted from the legacy Jobs coordinator.
 *
 * Public method names and parent Livewire state are intentionally preserved so
 * existing Blade bindings, deep links, validation keys and realtime behavior do
 * not change during the incremental decomposition.
 */
trait ManagesOrderInquiryLink
{
    public function updatedInquirySearch(): void
    {
        $this->selectedLinkInquiryId = null;
        $this->showInquiryLinkConfirm = false;
        $this->resetValidation('inquiryLink');
    }

    public function clearInquirySearch(): void
    {
        $this->inquirySearch = '';
        $this->selectedLinkInquiryId = null;
        $this->showInquiryLinkConfirm = false;
        $this->resetValidation('inquiryLink');
    }

    public function selectInquiryForLink(int $inquiryId): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'inquiry', 422);

        $user = auth()->user();
        $job = app(VisibleOrderQuery::class)->base($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canEditVisibleJob($user, $job), 403);

        $inquiry = app(InquiryDetailQuery::class)->find(
            $user,
            $inquiryId,
            ['sourceOrder:id,source_inquiry_id', 'convertedJob:id'],
        );

        $alreadyLinked = $inquiry->sourceOrder !== null
            || ($inquiry->converted_job_id && (int) $inquiry->converted_job_id !== (int) $job->id);
        abort_if($alreadyLinked, 409, 'This Inquiry is already linked to another Order.');
        abort_if((string) $inquiry->result === 'dead', 422, 'A closed Inquiry cannot be linked.');

        $this->selectedLinkInquiryId = $inquiry->id;
        $this->showInquiryLinkConfirm = false;
        $this->resetValidation('inquiryLink');
    }

    public function openInquiryLinkConfirm(): void
    {
        abort_unless($this->selectedJobId && $this->selectedLinkInquiryId, 422);
        $this->showInquiryLinkConfirm = true;
        $this->resetValidation('inquiryLink');
    }

    public function closeInquiryLinkConfirm(): void
    {
        $this->showInquiryLinkConfirm = false;
    }

    public function confirmInquiryLink(): void
    {
        abort_unless($this->selectedJobId && $this->selectedLinkInquiryId, 422);

        try {
            $user = auth()->user();
            app(LinkOrderInquiry::class)->handle($user, $this->selectedJobId, $this->selectedLinkInquiryId);

            $this->showInquiryLinkConfirm = false;
            $this->selectedLinkInquiryId = null;
            $this->inquirySearch = '';
            $this->resetValidation('inquiryLink');
            session()->flash('success', 'Inquiry linked successfully.');
        } catch (Throwable $exception) {
            report($exception);
            $this->showInquiryLinkConfirm = false;
            $message = trim($exception->getMessage());
            $this->addError('inquiryLink', $message !== '' ? $message : 'The Inquiry could not be linked. Please try again.');
        }
    }

    public function openInquiryUnlinkConfirm(): void
    {
        abort_unless($this->selectedJobId, 422);
        $this->showInquiryUnlinkConfirm = true;
        $this->resetValidation('inquiryLink');
    }

    public function closeInquiryUnlinkConfirm(): void
    {
        $this->showInquiryUnlinkConfirm = false;
    }

    public function confirmInquiryUnlink(): void
    {
        abort_unless($this->selectedJobId, 422);

        try {
            $user = auth()->user();
            app(UnlinkOrderInquiry::class)->handle($user, $this->selectedJobId);

            $this->showInquiryUnlinkConfirm = false;
            $this->selectedLinkInquiryId = null;
            $this->inquirySearch = '';
            $this->resetValidation('inquiryLink');
            session()->flash('success', 'Inquiry unlinked and activity recorded.');
        } catch (Throwable $exception) {
            report($exception);
            $this->showInquiryUnlinkConfirm = false;
            $message = trim($exception->getMessage());
            $this->addError('inquiryLink', $message !== '' ? $message : 'The Inquiry could not be unlinked. Please try again.');
        }
    }

}
