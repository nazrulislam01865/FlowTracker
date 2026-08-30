<?php

namespace App\Livewire\Jobs\Concerns;

use App\Actions\Orders\CreateOrder;
use App\Models\ClientShippingAddress;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowPhase;
use App\Services\ClientService;
use App\Services\MasterDataService;
use App\Services\OrderWorkflowSetupService;
use App\Support\AttachmentUpload;
use Illuminate\Validation\Rule;

/**
 * Phase 5 Order UI workflow extracted from the legacy Jobs coordinator.
 *
 * Public method names and parent Livewire state are intentionally preserved so
 * existing Blade bindings, deep links, validation keys and realtime behavior do
 * not change during the incremental decomposition.
 */
trait ManagesOrderCreation
{
    public function updatedWorkflowId(): void
    {
        if ($this->showCreate) $this->setDefaultStartPhase();
    }

    public function updatedIsRepeatedOrder(bool $value): void
    {
        if ($value) {
            $this->resetValidation('repeatedOrderNumber');
            return;
        }

        $this->repeatedOrderNumber = '';
        $this->resetValidation('repeatedOrderNumber');
    }

    public function setCreateSelector(string $property, mixed $value): void
    {
        abort_unless($this->showCreate && auth()->user()->canAccess('jobs.create'), 403);

        $user = auth()->user();
        $raw = trim((string) $value);
        $options = app(\App\Services\FilterOptionService::class);

        // Create Order selects only complete Order workflows from the shared
        // Workflow Setup. Inquiry workflows never appear in this picker.
        if ($property === 'workflowId') {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid Order workflow.');
            $id = (int) $raw;
            abort_unless(
                $this->createOrderWorkflowAvailableForClient($id, $this->clientId),
                422,
                'That Order workflow is no longer available.'
            );

            $this->workflowId = $id;
            $this->setDefaultStartPhase();
            $this->resetValidation('workflowId');
            $this->resetValidation('workflowPhaseId');
            return;
        }

        if (in_array($property, ['clientId', 'ownerId'], true)) {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid option.');
            $id = (int) $raw;
            $type = $property === 'clientId' ? 'clients' : 'users';

            $valid = $options->options($user, $type, 'create-job', '', $id, 20)
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($valid, 422, 'That option is no longer available.');

            $this->{$property} = $id;
            $this->resetValidation($property);

            if ($property === 'clientId') {
                // Re-resolve the preferred client-available Order workflow after
                // a Client change so Create Order never retains a stale id.
                $this->applyClientWorkflowDefault($id);
                $this->resetCreateShippingAddress();
            }

            return;
        }

        if (preg_match('/^jobItems\.(\d+)\.(category|product)$/', $property, $matches) !== 1) {
            abort(422, 'Unsupported Create Order selector.');
        }

        $this->authorizeCreateOrderProducts();
        $index = (int) $matches[1];
        $field = $matches[2];
        abort_unless(array_key_exists($index, $this->jobItems), 422, 'That product row is no longer available.');
        abort_unless($raw !== '', 422, 'Please choose a valid option.');

        $category = $field === 'product' ? trim((string) ($this->jobItems[$index]['category'] ?? '')) : '';
        $type = $field === 'category' ? 'product-categories' : 'products';
        $valid = $options->options(
            $user,
            $type,
            'create-job',
            '',
            $raw,
            20,
            $field === 'product' ? ['category' => $category] : [],
        )->contains(fn ($item) => (string) ($item['id'] ?? '') === $raw);
        abort_unless($valid, 422, 'That option is no longer available.');

        $this->jobItems[$index][$field] = $raw;
        $this->resetValidation("jobItems.$index.$field");

        if ($field === 'category') {
            // A Product is scoped to its category; changing the category must
            // invalidate the previous Product before the next render.
            $this->jobItems[$index]['product'] = '';
            $this->resetValidation("jobItems.$index.product");
        }
    }

    private function canUseCreateOrderProducts(User $user): bool
    {
        // Create-Order catalogue access is owned by the Products module. The
        // legacy Inquiry / Order Product Lines permission does not participate
        // in catalogue visibility or Product creation on this screen.
        return $user->canModule('jobs', 'create')
            && $user->canModule('catalog_products', 'view');
    }

    private function authorizeCreateOrderProducts(): void
    {
        abort_unless($this->showCreate && $this->canUseCreateOrderProducts(auth()->user()), 403);
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->canModule('jobs', 'create'), 403);
        $this->selectedJobId = null;
        $this->selectedTaskId = null;
        $this->showCreate = true;
        $this->initializeCreateForm();
    }

    public function closeCreate(): void
    {
        $this->resetCreateForm();
        $this->redirectRoute('jobs.index', navigate: true);
    }

    public function loadCreateSection(string $section): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('jobs', 'create'), 403);

        if ($section === 'catalog') {
            $this->createCatalogReady = true;
            return;
        }

        if ($section === 'assignment') {
            $this->createCatalogReady = true;
            $this->ownerId ??= auth()->id();
            $this->coordinatorId ??= auth()->id();
            $this->createAssignmentReady = true;
            return;
        }

        if ($section === 'workflow') {
            $this->createCatalogReady = true;
            // Backward-compatible repair for the historical ORDER_PROCESS
            // workflow before the shared Workflow Setup picker calculates tasks.
            app(OrderWorkflowSetupService::class)->repairIfIncomplete();
            $this->createAssignmentReady = true;
            $this->ownerId ??= auth()->id();
            $this->coordinatorId ??= auth()->id();

            // The Client may have changed while this lazy section was still a
            // placeholder. Never hydrate the Workflow selector with an old or
            // no-longer-available Workflow from the previous Client.
            if (!$this->workflowId || !$this->createOrderWorkflowAvailableForClient($this->workflowId, $this->clientId)) {
                $this->applyClientWorkflowDefault($this->clientId);
            } else {
                $this->setDefaultStartPhase();
            }

            $this->createWorkflowReady = true;
            return;
        }

        abort(422, 'Unknown Create Order section.');
    }

    public function openSavedShippingAddressPicker(): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('jobs', 'create'), 403);
        abort_unless($this->clientId, 422, 'Select a client first.');

        $clientAvailable = app(ClientService::class)
            ->referenceQuery(auth()->user(), 'create-job')
            ->where('is_active', true)
            ->whereKey($this->clientId)
            ->exists();
        abort_unless($clientAvailable, 403);

        $hasSavedAddress = ClientShippingAddress::query()
            ->where('client_id', $this->clientId)
            ->exists();

        if (!$hasSavedAddress) {
            $this->addError('shippingAddress', 'The selected client does not have a saved shipping address yet.');
            return;
        }

        $this->resetValidation('shippingAddress');
        $this->showSavedShippingAddressPicker = true;
    }

    public function closeSavedShippingAddressPicker(): void
    {
        $this->showSavedShippingAddressPicker = false;
    }

    public function useSavedShippingAddress(int $addressId): void
    {
        abort_unless($this->showCreate && auth()->user()->canModule('jobs', 'create'), 403);
        abort_unless($this->clientId, 422, 'Select a client first.');

        $clientAvailable = app(ClientService::class)
            ->referenceQuery(auth()->user(), 'create-job')
            ->where('is_active', true)
            ->whereKey($this->clientId)
            ->exists();
        abort_unless($clientAvailable, 403);

        $address = ClientShippingAddress::query()
            ->where('client_id', $this->clientId)
            ->findOrFail($addressId);

        $lines = collect([
            trim((string) $address->recipient),
            trim(collect([$address->address_line1, $address->suite])->filter(fn ($part) => filled($part))->implode(', ')),
            trim(collect([$address->city, $address->state, $address->country])->filter(fn ($part) => filled($part))->implode(', ')),
        ])->filter(fn ($line) => $line !== '')->values();

        $this->shippingAddress = $lines->implode("\n");
        $this->shippingPostalCode = trim((string) $address->zip);
        $this->shippingSourceAddressId = $address->id;
        $this->showSavedShippingAddressPicker = false;
        $this->resetValidation([
            'shippingAddress',
            'shippingPostalCode',
            'shippingSourceAddressId',
        ]);
    }

    private function resetCreateShippingAddress(): void
    {
        $this->shippingAddress = '';
        $this->shippingPhoneCountryCode = '';
        $this->shippingPhone = '';
        $this->shippingPostalCode = '';
        $this->shippingSourceAddressId = null;
        $this->showSavedShippingAddressPicker = false;
        $this->resetValidation([
            'shippingAddress',
            'shippingPhoneCountryCode',
            'shippingPhone',
            'shippingPostalCode',
            'shippingSourceAddressId',
        ]);
    }

    public function addProductRow(): void { $this->focusCreateProductSearch(); }

    public function removeProductRow(int $index): void
    {
        if (!array_key_exists($index, $this->jobItems)) return;

        $productId = (int) ($this->jobItems[$index]['product_id'] ?? 0);
        unset($this->jobItems[$index]);
        $this->jobItems = array_values($this->jobItems);

        if ($productId > 0) {
            $this->createOrderSupplierSkipProductIds = array_values(array_filter(
                $this->createOrderSupplierSkipProductIds,
                fn (int $id): bool => $id !== $productId
            ));
            unset($this->createOrderSupplierOverrides[$productId]);
        }

        $this->resetValidation('jobItems');
    }

    public function selectCreateProductionUrgency(int $urgencyId): void
    {
        $this->productionUrgencyIds = [$urgencyId];
        $this->resetErrorBag('productionUrgencyIds');
    }

    public function selectCreateShipmentUrgency(int $urgencyId): void
    {
        $this->shipmentUrgencyIds = [$urgencyId];
        $this->resetErrorBag('shipmentUrgencyIds');
    }

    public function createJob(): void { $this->persistJob(false); }

    public function saveDraft(): void { $this->persistJob(true); }

    /**
     * Resolve each Create Order supplier safely before validation.
     *
     * Product Master supplies the default, while an explicit Order-only override is
     * preserved when it still references an active supplier. A missing Product supplier
     * still requires an explicit user choice. Products
     * explicitly stored in createOrderSupplierSkipProductIds are allowed to continue
     * with supplier_id = null. The skip state stays outside jobItems so
     * the persisted Order item payload remains identical to the normal create flow.
     *
     * @return bool False when at least one selected Product is missing a supplier
     *              and the user has not explicitly skipped it.
     */
    private function synchronizeCreateOrderProductSuppliersFromCatalog(): bool
    {
        if ($this->jobItems === []) return true;

        $catalog = app(\App\Services\ProductCatalogService::class);
        $products = $catalog->selectedProducts(collect($this->jobItems)->pluck('product_id'));
        $defaultSuppliers = $catalog->suppliersForProducts($products);
        $overrideIds = collect($this->createOrderSupplierOverrides)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();
        $overrideSuppliers = $overrideIds->isEmpty()
            ? collect()
            : MasterRecord::query()
                ->forWorkspace(app(MasterDataService::class)->workspaceId())
                ->ofType('supplier')
                ->active()
                ->whereIn('id', $overrideIds->all())
                ->get(['id', 'name', 'code', 'status'])
                ->keyBy('id');
        $missingSupplier = false;

        foreach ($this->jobItems as $index => $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            $product = $products->get($productId);
            if (!$product) continue;

            $overrideId = (int) ($this->createOrderSupplierOverrides[$productId] ?? 0);
            $overrideSupplier = $overrideId > 0 ? $overrideSuppliers->get($overrideId) : null;
            $supplier = $overrideSupplier ?: $defaultSuppliers->get($productId);
            $this->jobItems[$index]['supplier_id'] = $supplier?->id;

            if ($supplier) {
                $this->createOrderSupplierSkipProductIds = array_values(array_filter(
                    $this->createOrderSupplierSkipProductIds,
                    fn (int $id): bool => $id !== $productId
                ));
                $this->resetValidation("jobItems.$index.supplier_id");
                continue;
            }

            if (in_array($productId, $this->createOrderSupplierSkipProductIds, true)) {
                $this->resetValidation("jobItems.$index.supplier_id");
                continue;
            }

            $missingSupplier = true;
            $this->addError(
                "jobItems.$index.supplier_id",
                'Supplier is not linked. Choose a supplier for this Order or choose “Skip supplier for now”.'
            );

            if (!$this->showMissingProductSupplierModal) {
                $this->missingProductSupplierName = (string) $product->name;
                $this->pendingMissingSupplierProductId = (int) $product->id;
                $this->pendingMissingSupplierRowIndex = $index;
                $this->showMissingProductSupplierModal = true;
            }
        }

        return !$missingSupplier;
    }

    private function persistJob(bool $draft): void
    {
        abort_unless($this->canUseCreateOrderProducts(auth()->user()), 403);

        if (!$this->createCatalogReady || !$this->createAssignmentReady || !$this->createWorkflowReady) {
            $this->addError('createLoading', 'Please wait for the remaining Create Order fields to finish loading.');
            return;
        }

        // Re-resolve defaults and validate any explicit Order-only supplier override
        // immediately before validation. This prevents stale or tampered supplier IDs.
        if (!$this->synchronizeCreateOrderProductSuppliersFromCatalog()) {
            return;
        }

        $data = $this->validate([
            'referenceNumber' => ['required','string','max:255'],
            'isRepeatedOrder' => ['boolean'],
            'repeatedOrderNumber' => [Rule::requiredIf($this->isRepeatedOrder), 'nullable', 'string', 'max:255'],
            'shipmentUrgencyIds' => ['array', 'max:1'],
            'shipmentUrgencyIds.*' => [
                'integer',
                Rule::exists('master_records', 'id')->where(fn ($query) => $query
                    ->where('workspace_id', app(MasterDataService::class)->workspaceId())
                    ->where('type', 'shipment_urgency')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
            'clientId' => ['required','exists:clients,id'],
            'workflowId' => ['required','integer'],
            'workflowPhaseId' => ['required','integer'],
            'ownerId' => ['required','exists:users,id'],
            'coordinatorId' => ['nullable','exists:users,id'],
            // Order hand date is intentionally optional on Create Order.
            // The DTO already normalizes an empty value to null and the database column is nullable.
            'deliveryDate' => ['nullable','date'],
            'estimatedDeliveryDate' => ['nullable','date'],
            'description' => ['nullable','string'],
            'shippingAddress' => ['required','string','max:2000'],
            'shippingPhoneCountryCode' => [
                'nullable',
                'string',
                'max:12',
                'regex:/^\+[0-9]{1,4}$/',
                Rule::exists('master_records', 'name')->where(fn ($query) => $query
                    ->where('workspace_id', app(MasterDataService::class)->workspaceId())
                    ->where('type', 'phone_country_code')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
            'shippingPhone' => ['nullable','string','max:60','regex:/^[0-9()\s.\-]{5,40}$/'],
            'shippingPostalCode' => ['required','string','max:30'],
            'shippingSourceAddressId' => [
                'nullable',
                'integer',
                Rule::exists('client_shipping_addresses', 'id')->where(fn ($query) => $query->where('client_id', $this->clientId)),
            ],
            'jobItems' => ['required','array','min:1','max:25'],
            'jobItems.*.product_id' => ['required','integer'],
            'jobItems.*.category' => ['required','string','max:255'],
            'jobItems.*.product' => ['required','string','max:255'],
            'jobItems.*.supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('master_records', 'id')->where(fn ($query) => $query
                    ->where('workspace_id', app(MasterDataService::class)->workspaceId())
                    ->where('type', 'supplier')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
            'jobItems.*.quantity' => ['required','integer','min:1','max:999999999'],
            'jobItems.*.unit_price' => ['nullable','numeric','min:0','max:999999999999.99'],
            'jobItems.*.notes' => ['nullable','string','max:2000'],
            // Optional PO uploaded during Create Order. It is attached to the
            // NEW_UPLOAD_PO workflow task after the Order and its tasks exist.
            'purchaseOrderUpload' => AttachmentUpload::nullableRules(AttachmentUpload::DOCUMENTS_WITH_AI, 20480),
            'jobAttachments.*' => AttachmentUpload::itemRules(AttachmentUpload::DOCUMENTS, 20480),
        ], [
            'referenceNumber.required' => 'Client Reference Number is required.',
            'repeatedOrderNumber.required' => 'Enter the previous reference number for this repeated Order.',
            'jobItems.required' => 'Select at least one product for this Order.',
            'jobItems.min' => 'Select at least one product for this Order.',
            'shippingAddress.required' => 'Shipping address is required.',
            'deliveryDate.date' => 'Order hand date must be a valid date.',
            'shippingPostalCode.required' => 'Postal code is required.',
            'shippingPhoneCountryCode.regex' => 'Choose a valid international phone code.',
            'shippingPhoneCountryCode.exists' => 'Choose an active phone country code from Master Data.',
            'shippingPhone.regex' => 'Enter a valid shipping contact phone number.',
            'purchaseOrderUpload.max' => 'The Purchase Order is too large. Maximum file size is 20 MB.',
        ]);

        if ($this->purchaseOrderUpload || count($this->jobAttachments) > 0) {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        }

        $clientAvailable = app(ClientService::class)
            ->referenceQuery(auth()->user(), 'create-job')
            ->where('is_active', true)
            ->whereKey((int) $data['clientId'])
            ->exists();
        if (!$clientAvailable) {
            $this->addError('clientId', 'That client is no longer available.');
            return;
        }

        $workflowAvailable = OrderWorkflowSetupService::orderWorkflowQuery()
            ->where('is_active', true)
            ->availableFor('orders', (int) $data['clientId'])
            ->whereKey((int) $data['workflowId'])
            ->exists()
            && app(OrderWorkflowSetupService::class)->isReadyForOrderCreation((int) $data['workflowId']);

        if (!$workflowAvailable) {
            $this->addError('workflowId', 'That Order workflow is not available. Choose a complete Order workflow from Workflow Setup.');
            return;
        }

        $firstOrderPhaseId = WorkflowPhase::query()
            ->where('workflow_template_id', (int) $data['workflowId'])
            ->where('is_active', true)
            ->orderBy('sequence')
            ->value('id');

        if (!$firstOrderPhaseId || (int) $data['workflowPhaseId'] !== (int) $firstOrderPhaseId) {
            $this->addError('workflowPhaseId', 'New Orders must start from Stage 1 of the selected Order workflow.');
            return;
        }

        $catalogInvalid = false;
        $workspaceId = app(MasterDataService::class)->workspaceId();
        foreach ($data['jobItems'] as $index => $row) {
            $product = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->active()
                ->with('parent:id,name,status')
                ->find((int) $row['product_id']);

            $valid = $product
                && $product->parent
                && $product->parent->status === 'active'
                && (string) $product->name === trim((string) $row['product'])
                && (string) $product->parent->name === trim((string) $row['category']);

            if (!$valid) {
                $catalogInvalid = true;
                $this->addError("jobItems.$index.product", 'That product is no longer available in the selected catalog.');
                continue;
            }

            // Product Master pricing is authoritative for Create Order. Resolve
            // the base/unit price again at save time so a stale browser value or
            // client-side tampering cannot override the quantity price table.
            $quantity = (int) ($row['quantity'] ?? 0);
            $basePrice = $product->productPriceForQuantity($quantity);
            $data['jobItems'][$index]['unit_price'] = $basePrice !== null
                ? round($basePrice, 2)
                : null;
        }
        if ($catalogInvalid) return;

        $job = app(CreateOrder::class)->handle(
            $data,
            $this->purchaseOrderUpload,
            $this->jobAttachments,
            $draft,
            auth()->user(),
        );

        $this->showCreate = false;
        $this->resetCreateForm();
        $this->selectedJobId = $job->id;
        $this->detailTab = 'overview';
        $this->prepareSelectedJob($job->id);
        session()->flash('success', $draft ? 'Order draft saved.' : 'Order created and all configured Workflow Task Packs were loaded.');
    }

    private function preferredCreateOrderWorkflowId(?int $clientId): ?int
    {
        $ids = OrderWorkflowSetupService::orderWorkflowQuery()
            ->where('is_active', true)
            ->availableFor('orders', $clientId)
            // A workflow configured specifically for the selected Client is
            // more intentional than an all-client default. Keep the default
            // flag as the tie-breaker within the same availability scope.
            ->orderByRaw("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END")
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $id) {
            if (app(OrderWorkflowSetupService::class)->isReadyForOrderCreation((int) $id)) return (int) $id;
        }

        return null;
    }

    private function createOrderWorkflowAvailableForClient(int $workflowId, ?int $clientId): bool
    {
        return OrderWorkflowSetupService::orderWorkflowQuery()
            ->where('is_active', true)
            ->availableFor('orders', $clientId)
            ->whereKey($workflowId)
            ->exists()
            && app(OrderWorkflowSetupService::class)->isReadyForOrderCreation($workflowId);
    }

    private function applyClientWorkflowDefault(?int $clientId): void
    {
        // Clear first so both Livewire and Alpine cannot temporarily retain the
        // previous Client's selection while the new default is being resolved.
        $this->workflowId = null;
        $this->workflowPhaseId = null;

        // Resolve the preferred ready Order workflow for the selected client.
        // Client availability is configured in the shared Workflow Setup.
        $this->workflowId = $this->preferredCreateOrderWorkflowId($clientId);
        $this->setDefaultStartPhase();

        // Force the remote Workflow selector to get a fresh Alpine instance.
        // Its request params include client_id, so reusing the old instance can
        // otherwise leave the dropdown searching with the previous Client.
        $this->createWorkflowSelectorVersion++;
        $this->resetValidation('workflowId');
        $this->resetValidation('workflowPhaseId');
    }

    private function setDefaultStartPhase(): void
    {
        if (!$this->workflowId) {
            $this->workflowPhaseId = null;
            return;
        }

        // Every Order workflow keeps the same fixed seven-stage runtime. New
        // Orders always enter at Stage 1; users select the workflow, not a stage.
        $this->workflowPhaseId = WorkflowPhase::query()
            ->where('workflow_template_id', $this->workflowId)
            ->where('is_active', true)
            ->orderBy('sequence')
            ->value('id');
    }

    private function initializeCreateForm(?int $requestedClientId = null): void
    {
        $this->resetCreateForm();
        $clientQuery = app(ClientService::class)
            ->referenceQuery(auth()->user(), 'create-job')
            ->where('is_active', true);

        $this->clientId = $requestedClientId && (clone $clientQuery)->whereKey($requestedClientId)->exists()
            ? $requestedClientId
            : $clientQuery->value('id');
        $this->applyClientWorkflowDefault($this->clientId);
        $this->shippingPhoneCountryCode = '';
        $this->jobItems = [];
    }

    private function resetCreateForm(): void
    {
        $this->resetValidation();
        $this->reset([
            'referenceNumber',
            'isRepeatedOrder',
            'repeatedOrderNumber',
            'priority',
            'productionUrgencyIds',
            'shipmentUrgencyIds',
            'clientId',
            'workflowId',
            'workflowPhaseId',
            'ownerId',
            'coordinatorId',
            'deliveryDate',
            'estimatedDeliveryDate',
            'description',
            'shippingAddress',
            'shippingPhoneCountryCode',
            'shippingPhone',
            'shippingPostalCode',
            'shippingSourceAddressId',
            'showSavedShippingAddressPicker',
            'jobItems',
            'createProductSearch',
            'createProductCategoryFilter',
            'createProductShowAllResults',
            'showCreateOrderProductModal',
            'showMissingProductSupplierModal',
            'missingProductSupplierName',
            'pendingMissingSupplierProductId',
            'pendingMissingSupplierRowIndex',
            'createOrderSupplierSkipProductIds',
            'createOrderSupplierOverrides',
            'newProductCode',
            'newProductCategoryId',
            'newProductCategorySearch',
            'newProductCategoryName',
            'newProductName',
            'newProductSupplierId',
            'newProductImage',
            'purchaseOrderUpload',
            'jobAttachments',
            'createCatalogReady',
            'createAssignmentReady',
            'createWorkflowReady',
            'createWorkflowSelectorVersion',
        ]);
    }

}
