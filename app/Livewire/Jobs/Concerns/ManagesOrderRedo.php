<?php

namespace App\Livewire\Jobs\Concerns;

use App\Models\FlowJob;
use App\Queries\Orders\VisibleOrderQuery;
use App\Services\OrderRedoService;
use App\Services\RichTextService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait ManagesOrderRedo
{
    public bool $showRedoModal = false;
    public int $redoStep = 1;
    public string $redoIssueSource = 'Customer';
    public string $redoIssueCategory = 'Artwork / production mismatch';
    public string $redoAffectedQuantity = '1';
    public string $redoIssueDescription = '';
    public string $redoScope = 'artwork';
    public string $redoQuantity = '1';
    public ?int $redoSupplierId = null;
    public string $redoInstructions = '';
    public string $redoCustomerResolution = 'free';
    public string $redoCustomerDiscount = '20';
    public string $redoSupplierChargePercent = "40";
    public bool $redoDeductFreight = true;
    public string $redoFreightAmount = '320.00';
    /** @var array<int,array{id:int,label:string}> */
    public array $redoSupplierOptions = [];
    /** @var array<int,string> */
    public array $redoEvidence = [];

    public function openRedoModal(): void
    {
        abort_unless($this->selectedJobId, 422);
        $job = app(VisibleOrderQuery::class)->base(auth()->user(), (int) $this->selectedJobId);
        abort_unless(app(OrderRedoService::class)->canInitiate(auth()->user(), $job), 403);

        $quantity = max(1, (int) $job->quantity);
        $this->redoStep = 1;
        $this->redoIssueSource = 'Customer';
        $this->redoIssueCategory = 'Artwork / production mismatch';
        $this->redoAffectedQuantity = (string) $quantity;
        $this->redoIssueDescription = '';
        $this->redoScope = 'artwork';
        $this->redoQuantity = (string) $quantity;
        $this->redoInstructions = '';
        $this->redoCustomerResolution = 'free';
        $this->redoCustomerDiscount = '20';
        $this->redoSupplierChargePercent = '40';
        $this->redoDeductFreight = true;
        $this->redoFreightAmount = '320.00';
        $this->redoSupplierOptions = app(OrderRedoService::class)->supplierOptions($job);
        $this->redoSupplierId = $this->redoSupplierOptions[0]['id'] ?? null;
        $this->redoEvidence = app(OrderRedoService::class)->evidenceLabels($job);
        $this->showRedoModal = true;
        $this->resetValidation([
            'redoIssueSource', 'redoIssueCategory', 'redoAffectedQuantity', 'redoIssueDescription',
            'redoScope', 'redoQuantity', 'redoSupplierId', 'redoInstructions',
            'redoCustomerResolution', 'redoCustomerDiscount', 'redoSupplierChargePercent', 'redoFreightAmount',
        ]);
    }

    public function closeRedoModal(): void
    {
        $this->showRedoModal = false;
        $this->redoStep = 1;
        $this->resetValidation([
            'redoIssueSource', 'redoIssueCategory', 'redoAffectedQuantity', 'redoIssueDescription',
            'redoScope', 'redoQuantity', 'redoSupplierId', 'redoInstructions',
            'redoCustomerResolution', 'redoCustomerDiscount', 'redoSupplierChargePercent', 'redoFreightAmount',
        ]);
    }

    public function updatedRedoAffectedQuantity($value): void
    {
        $quantity = max(1, (int) $value);
        $this->redoAffectedQuantity = (string) $quantity;
        if ($this->redoStep <= 1) $this->redoQuantity = (string) $quantity;
    }

    public function updatedRedoQuantity($value): void
    {
        $quantity = max(1, (int) $value);
        $this->redoQuantity = (string) $quantity;
        $this->redoAffectedQuantity = (string) $quantity;
    }

    public function updatedRedoScope($value): void
    {
        if ((string) $value === 'discount') {
            // Discount is an alternative to operational redo. Keep the
            // financial calculation tied to the affected quantity but do not
            // prepare any supplier/workflow restart defaults.
            $this->redoCustomerResolution = 'discount';
            $this->redoQuantity = (string) max(1, (int) $this->redoAffectedQuantity);
            $this->redoSupplierId = null;
            $this->redoSupplierChargePercent = "0";
            $this->redoDeductFreight = false;
            $this->redoFreightAmount = '0.00';
            return;
        }

        // If the user changes back from Discount to an operational redo scope,
        // restore the normal redo defaults instead of carrying the discount-only
        // zero values into Artwork/Production by accident.
        if ((float) $this->redoSupplierChargePercent === 0.0
            && !$this->redoDeductFreight
            && (float) $this->redoFreightAmount === 0.0
            && $this->redoSupplierId === null
            && $this->redoCustomerResolution === 'discount') {
            $this->redoCustomerResolution = 'free';
            $this->redoSupplierChargePercent = '40';
            $this->redoDeductFreight = true;
            $this->redoFreightAmount = '320.00';
            $this->redoSupplierId = $this->redoSupplierOptions[0]['id'] ?? null;
        }
    }

    public function nextRedoStep(): void
    {
        abort_unless($this->showRedoModal && $this->selectedJobId, 422);

        if ($this->redoStep === 1) {
            $this->validateRedoIssueStep();
            $this->redoStep = 2;
            return;
        }

        if ($this->redoStep === 2) {
            $this->validateRedoScopeStep();
            $this->redoStep = 3;
            return;
        }

        if ($this->redoStep === 3) {
            $this->validateRedoCommercialStep();
            $this->redoStep = 4;
            return;
        }

        $this->createRedoOrder();
    }

    public function previousRedoStep(): void
    {
        abort_unless($this->showRedoModal, 422);
        $this->redoStep = max(1, $this->redoStep - 1);
    }

    public function createRedoOrder(): void
    {
        abort_unless($this->showRedoModal && $this->selectedJobId, 422);
        $this->validateRedoIssueStep();
        $this->validateRedoScopeStep();
        $this->validateRedoCommercialStep();

        $job = app(VisibleOrderQuery::class)->base(auth()->user(), (int) $this->selectedJobId);
        $record = app(OrderRedoService::class)->createRedo($job, [
            'issue_reported_by' => $this->redoIssueSource,
            'issue_category' => $this->redoIssueCategory,
            'affected_quantity' => $this->redoAffectedQuantity,
            'issue_description' => $this->redoIssueDescription,
            'scope' => $this->redoScope,
            'redo_quantity' => $this->redoQuantity,
            'supplier_id' => $this->redoSupplierId,
            'internal_instructions' => $this->redoInstructions,
            'customer_resolution' => $this->redoCustomerResolution,
            'customer_discount_percent' => (float) $this->redoCustomerDiscount,
            'supplier_redo_charge_percent' =>
                (float) $this->redoSupplierChargePercent,
            'deduct_freight' => $this->redoDeductFreight,
            'freight_amount' => (float) $this->redoFreightAmount,
        ], auth()->user());

        $redoOrderId = (int) ($record->redo_order_id ?? 0);
        $redoOrderNumber = $record->redoOrder?->displayOrderNumber() ?: 'Redo order';

        $this->showRedoModal = false;
        $this->redoStep = 1;
        $this->resetValidation();

        if ($record->scope === 'discount' || $redoOrderId <= 0) {
            // Discount-instead-of-redo is financial only. Stay on the original
            // Order, keep its workflow/tasks untouched, and show the recorded
            // adjustment in the Redo tab immediately.
            $this->detailTab = 'redo';
            $message = rtrim(rtrim(number_format((float) $record->customer_discount_percent, 2), '0'), '.')
                .'% customer discount recorded. The Order workflow was not restarted.';
        } else {
            // Operational redo: open the NEW Redo Order immediately so the
            // selected restart phase is visible and actionable.
            $this->openJob($redoOrderId);
            $this->detailTab = 'overview';
            $message = $redoOrderNumber.' created and restarted from the selected Redo phase.';
        }

        session()->flash('success', $message);
        $this->dispatch('order-redo-notice', message: $message);
    }

    public function openLinkedRedoOrder(int $orderId): void
    {
        $this->openJob($orderId);
    }

    private function validateRedoIssueStep(): void
    {
        $maxQuantity = $this->redoSourceMaxQuantity();
        $this->validate([
            'redoIssueSource' => ['required', Rule::in(['Customer', 'Quality Control', 'Internal Team'])],
            'redoIssueCategory' => ['required', 'string', 'max:120'],
            'redoAffectedQuantity' => ['required', 'integer', 'min:1', 'max:'.$maxQuantity],
            'redoIssueDescription' => ['required', 'string', 'max:60000'],
        ], [], [
            'redoIssueSource' => 'issue reported by',
            'redoIssueCategory' => 'issue category',
            'redoAffectedQuantity' => 'affected quantity',
            'redoIssueDescription' => 'issue description',
        ]);

        // The Redo reason uses FlowTrack's shared rich-text format. Normalize it
        // here so pasted images are whitelisted, unsafe HTML is removed, and
        // the 5,000-character limit applies to readable text instead of markup.
        $normalized = app(RichTextService::class)->normalize(
            $this->redoIssueDescription,
            5000,
            'redoIssueDescription',
        );

        if ($normalized === null) {
            throw ValidationException::withMessages([
                'redoIssueDescription' => 'Add an issue description or pasted image.',
            ]);
        }

        $this->redoIssueDescription = $normalized;
    }

    private function validateRedoScopeStep(): void
    {
        $maxQuantity = $this->redoSourceMaxQuantity();
        $supplierIds = collect($this->redoSupplierOptions)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $rules = [
            'redoScope' => ['required', Rule::in(['artwork', 'production', 'discount'])],
            'redoQuantity' => ['required', 'integer', 'min:1', 'max:'.$maxQuantity],
            'redoInstructions' => ['nullable', 'string', 'max:5000'],
        ];
        if ($this->redoSupplierId !== null) {
            $rules['redoSupplierId'] = $supplierIds !== []
                ? ['nullable', 'integer', Rule::in($supplierIds)]
                : ['nullable', 'integer'];
        }

        $this->validate($rules, [], [
            'redoScope' => 'redo scope',
            'redoQuantity' => 'redo quantity',
            'redoSupplierId' => 'responsible supplier',
            'redoInstructions' => 'internal instructions',
        ]);
    }

    private function validateRedoCommercialStep(): void
    {
        if ($this->redoScope === 'discount') {
            $this->redoCustomerResolution = 'discount';
        }

        $this->validate([
            'redoCustomerResolution' => ['required', Rule::in(['free', 'discount'])],
            'redoCustomerDiscount' => [
                                            'required',
                                            'numeric',
                                            'min:0',
                                            'max:100',
                                        ],
            'redoSupplierChargePercent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'redoDeductFreight' => ['boolean'],
            'redoFreightAmount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
        ], [], [
            'redoCustomerResolution' => 'customer resolution',
            'redoCustomerDiscount' => 'customer discount',
            'redoSupplierChargePercent' => 'supplier redo charge',
            'redoFreightAmount' => 'freight amount',
        ]);
    }

    private function redoSourceMaxQuantity(): int
    {
        if (!$this->selectedJobId) return 1;
        $job = FlowJob::query()->find($this->selectedJobId, ['id', 'quantity']);
        return max(1, (int) ($job?->quantity ?: 1));
    }

    /** @return array<string,mixed> */
    private function redoFormState(FlowJob $job): array
    {
        $effectiveQuantity = $this->redoScope === 'discount'
            ? max(1, (int) $this->redoAffectedQuantity)
            : max(1, (int) $this->redoQuantity);
        $effectiveResolution = $this->redoScope === 'discount'
            ? 'discount'
            : $this->redoCustomerResolution;

        $preview = $this->showRedoModal
            ? app(OrderRedoService::class)->financialPreview(
                $job,
                $effectiveQuantity,
                $effectiveResolution,
                (float) $this->redoCustomerDiscount,
                (float) $this->redoSupplierChargePercent,
                $this->redoDeductFreight,
                (float) $this->redoFreightAmount,
            )
            : [
                'quantity' => 0,
                'unitValue' => 0.0,
                'affectedValue' => 0.0,
                'customerImpact' => 0.0,
                'supplierCharge' => 0.0,
                'freight' => 0.0,
                'recovery' => 0.0,
            ];

        return [
            'show' => $this->showRedoModal,
            'step' => $this->redoStep,
            'issueSource' => $this->redoIssueSource,
            'issueCategory' => $this->redoIssueCategory,
            'affectedQuantity' => $this->redoAffectedQuantity,
            'issueDescription' => $this->redoIssueDescription,
            'scope' => $this->redoScope,
            'quantity' => $this->redoQuantity,
            'supplierId' => $this->redoSupplierId,
            'instructions' => $this->redoInstructions,
            'customerResolution' => $effectiveResolution,
            'customerDiscount' => $this->redoCustomerDiscount,
            'supplierChargePercent' => $this->redoSupplierChargePercent,
            'deductFreight' => $this->redoDeductFreight,
            'freightAmount' => $this->redoFreightAmount,
            'supplierOptions' => $this->redoSupplierOptions,
            'evidence' => $this->redoEvidence,
            'preview' => $preview,
        ];
    }
}
