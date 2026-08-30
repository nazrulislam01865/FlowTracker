<?php

namespace App\Livewire\MasterData\Concerns;

use App\Models\Client;
use App\Models\MasterRecord;
use App\Support\Filters\ProductClientOptions;
use App\Services\MasterDataService;
use App\Services\ProductImageService;
use App\Services\ProductOptionImageService;
use App\Services\ProductPriceTableParser;
use App\Services\ProductCategoryDeletionService;
use App\Support\MasterColor;
use App\Support\AttachmentUpload;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait ManagesMasterEditor
{
    public function open(?int $id = null): void
    {
        $action = $id ? 'edit' : 'create';
        $this->authorizeGroupAction($action);
        $service = app(MasterDataService::class);
        if ($this->group === 'product' && $id) {
            app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();
        }
        // Product create/edit is a full-page editor. Do not hydrate the product
        // list behind it; direct ?create=1 navigation should pay only for the
        // form and the progressive sections the user reaches. Compact generic
        // Master Data editors still keep their list available behind the modal.
        if ($this->group !== 'product') {
            $this->recordsReady = true;
        }
        $this->showModal = true;
        $this->editId = $id;
        // Existing products need their stored taxonomy/urgency values immediately.
        // New products hydrate these below-the-fold reference datasets only when
        // their sections approach the viewport.
        $this->productTaxonomyReady = (bool) $id;
        $this->productShipmentOptionsReady = (bool) $id;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->productSupplierId = null;
        $this->productCertificateUpload = null;
        $this->productTemplateUpload = null;
        $this->productOptions = [];
        $this->productOptionUploads = [];
        $this->productShipmentUrgencies = [];
        $this->productShipmentUrgencyPickerOpen = false;
        $this->productShipmentUrgencyPickerSelection = [];
        $this->removeProductCertificate = false;
        $this->removeProductTemplate = false;
        $this->resetCategoryCreatorState();
        $this->workCalendarDayFrom = 'monday';
        $this->workCalendarDayTo = 'friday';
        $this->workCalendarTimeFrom = '09:00';
        $this->workCalendarTimeTo = '18:00';
        $this->resetValidation();

        if ($id) {
            $r = MasterRecord::where('workspace_id', $service->workspaceId())->findOrFail($id);
            abort_unless($r->type === $this->group, 404);
            $this->code = $r->code;
            $this->name = $r->name;
            $this->description = (string) $r->description;
            $this->productReferenceCode = $r->productReferenceCode();
            $this->productSupplierId = $r->productSupplierId();
            $this->productFormMainCategory = $r->productMainCategory();
            $this->productSize = $r->productSize();
            $this->productPriceTable = trim((string) data_get($r->metadata, 'price_table_raw'));
            $storedPriceBreakpoints = collect((array) data_get($r->metadata, 'price_breakpoints', []))
                ->map(fn ($row) => [
                    'quantity' => (int) data_get($row, 'quantity', 0),
                    'price' => (float) data_get($row, 'price', 0),
                ])
                ->filter(fn ($row) => $row['quantity'] > 0 && $row['price'] >= 0)
                ->sortBy('quantity')->values()->all();
            $storedRemoteSurchargeBreakpoints = collect((array) data_get($r->metadata, 'remote_surcharge_breakpoints', []))
                ->map(fn ($row) => [
                    'quantity' => (int) data_get($row, 'quantity', 0),
                    'price' => (float) data_get($row, 'price', 0),
                ])
                ->filter(fn ($row) => $row['quantity'] > 0 && $row['price'] >= 0)
                ->sortBy('quantity')->values()->all();

            if ($this->productPriceTable !== '') {
                $parsedPriceTable = app(ProductPriceTableParser::class)->parseTable($this->productPriceTable);
                $this->productPricePreview = $parsedPriceTable['price_breakpoints'];
                $this->productRemoteSurchargePreview = $parsedPriceTable['remote_surcharge_breakpoints'];
            } else {
                $this->productPricePreview = $storedPriceBreakpoints;
                $this->productRemoteSurchargePreview = $storedRemoteSurchargeBreakpoints;
            }

            if ($this->productPriceTable === '' && $this->productPricePreview !== []) {
                $quantities = collect($this->productPricePreview)->pluck('quantity')->implode("	");
                $prices = collect($this->productPricePreview)->pluck('price')->implode("	");
                $this->productPriceTable = "Quantity	".$quantities."
Product price	".$prices;
                if ($this->productRemoteSurchargePreview !== []) {
                    $remotePrices = collect($this->productRemoteSurchargePreview)->keyBy('quantity');
                    $remoteRow = collect($this->productPricePreview)
                        ->map(fn ($row) => data_get($remotePrices->get($row['quantity']), 'price', ''))
                        ->implode("	");
                    $this->productPriceTable .= "
Remote Area charge	".$remoteRow;
                }
            }
            $this->productOptions = $r->productOptions();
            $this->productOptionUploads = [];
            $this->productShipmentUrgencies = collect($r->productShipmentUrgencyOptions())
                ->map(fn (array $option) => [
                    'key' => trim((string) ($option['key'] ?? '')) ?: (string) Str::uuid(),
                    'shipment_urgency_id' => (string) ($option['shipment_urgency_id'] ?? ''),
                    'extra_charge' => (float) ($option['extra_charge'] ?? 0) > 0 ? (string) $option['extra_charge'] : '',
                ])->values()->all();
            $this->productSubcategory = trim((string) (data_get($r->metadata, 'sub_category') ?: data_get($r->metadata, 'excel_sub_category')));
            $this->productClientAvailabilityMode = $r->hasSpecificProductAvailability() ? 'specific' : 'all';
            $storedClientIds = collect((array) data_get($r->metadata, 'client_ids', []))->map(fn ($value) => (int) $value)->filter()->values()->all();
            if (!$storedClientIds && $r->hasSpecificProductAvailability()) {
                $labels = collect($r->productAvailabilityLabels())
                    ->map(fn ($value) => mb_strtolower(trim((string) $value)))
                    ->filter()
                    ->unique()
                    ->values();
                $storedClientIds = $labels->isEmpty()
                    ? []
                    : Client::query()
                        ->where('is_active', true)
                        ->where(function ($query) use ($labels): void {
                            foreach ($labels as $label) {
                                $query->orWhereRaw('LOWER(TRIM(name)) = ?', [$label])
                                    ->orWhereRaw('LOWER(TRIM(code)) = ?', [$label]);
                            }
                        })
                        ->pluck('id')
                        ->map(fn ($value) => (int) $value)
                        ->values()
                        ->all();
            }
            $this->productClientIds = $storedClientIds;
            $this->productTestCertificateNumber = trim((string) data_get($r->metadata, 'test_certificate_number'));
            $this->color = MasterColor::normalize($r->color) ?: MasterColor::defaultFor($this->group, $r->name);
            $this->parentId = in_array($this->group, ['product', 'state'], true) ? $r->parent_id : null;
            if ($this->group === 'product' && $r->parent_id) {
                $this->productCategorySearch = (string) $r->parent?->name;
            }
            $this->status = $r->status;
            $this->sortOrder = (int) $r->sort_order;
            $this->existingProductImageUrl = $r->productImageUrl();
            $metadata = (array) ($r->metadata ?? []);
            if ($this->group === 'task_pack_work_calendar') {
                $this->workCalendarDayFrom = strtolower(trim((string) ($metadata['day_from'] ?? 'monday'))) ?: 'monday';
                $this->workCalendarDayTo = strtolower(trim((string) ($metadata['day_to'] ?? 'friday'))) ?: 'friday';
                $this->workCalendarTimeFrom = trim((string) ($metadata['time_from'] ?? '09:00')) ?: '09:00';
                $this->workCalendarTimeTo = trim((string) ($metadata['time_to'] ?? '18:00')) ?: '18:00';
            }
            if ($this->group === 'inquiry_task_status') {
                $this->autoInquiryStatus = (string) ($metadata['auto_inquiry_status'] ?? '__task_status__');
                $this->requiresAttention = filter_var($metadata['requires_attention'] ?? false, FILTER_VALIDATE_BOOL);
            } else {
                $this->autoInquiryStatus = 'To do';
                $this->requiresAttention = false;
            }
            $this->orderTaskFlagId = $this->group === 'order_task_status'
                ? ((int) ($metadata['order_task_flag_id'] ?? 0) ?: null)
                : null;
            $this->orderFlagId = $this->group === 'order_task_flag'
                ? ((int) ($metadata['order_flag_id'] ?? 0) ?: null)
                : null;
            unset($metadata['product_image_path']);
            $this->metadataJson = $metadata ? json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
            return;
        }

        $this->reset(['code', 'name', 'description', 'parentId', 'metadataJson']);
        $this->color = MasterColor::defaultFor($this->group);
        $this->code = $service->nextCode($this->group);
        if ($this->group === 'product') {
            $this->productReferenceCode = '';
            $this->productSupplierId = null;
            $this->productFormMainCategory = '';
            $this->productSize = '';
            $this->productPriceTable = '';
            $this->productPricePreview = [];
            $this->productRemoteSurchargePreview = [];
            $this->productOptions = [];
            $this->productOptionUploads = [];
            $this->productShipmentUrgencies = [];
            $this->productShipmentUrgencyPickerOpen = false;
            $this->productShipmentUrgencyPickerSelection = [];
            $this->productSubcategory = '';
            $this->productClientAvailabilityMode = 'all';
            $this->productClientIds = [];
            $this->productTestCertificateNumber = '';
        }
        $this->status = 'active';
        $this->autoInquiryStatus = 'To do';
        $this->requiresAttention = false;
        $this->orderTaskFlagId = null;
        $this->orderFlagId = null;
        $this->sortOrder = (int) MasterRecord::where('workspace_id', $service->workspaceId())->where('type', $this->group)->max('sort_order') + 1;
    }
    public function loadCreateSection(string $section): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 422);

        if ($section === 'product-taxonomy') {
            if (! $this->productTaxonomyReady) {
                app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();
                $this->productTaxonomyReady = true;
            }
            return;
        }

        if ($section === 'product-shipping-urgencies') {
            $this->productShipmentOptionsReady = true;
            return;
        }

        abort(422, 'Unknown Create Product section.');
    }

    public function close(): void
    {
        $this->showModal = false;
        if ($this->group === 'product') {
            $this->recordsReady = true;
        }
        $this->productTaxonomyReady = false;
        $this->productShipmentOptionsReady = false;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->productCertificateUpload = null;
        $this->productTemplateUpload = null;
        $this->productOptions = [];
        $this->productOptionUploads = [];
        $this->productShipmentUrgencies = [];
        $this->productShipmentUrgencyPickerOpen = false;
        $this->productShipmentUrgencyPickerSelection = [];
        $this->removeProductCertificate = false;
        $this->removeProductTemplate = false;
        $this->resetCategoryCreatorState();
        $this->resetValidation();
    }
    public function save(): void
    {
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();

        // Product display codes are generated from the record id. The underlying
        // master-data code stays a unique internal key; the supplier/reference
        // code is stored separately in metadata.
        if (!$this->editId) {
            $this->code = $service->nextCode($this->group);
        }

        $data = $this->validate([
            'code' => ['required', 'string', 'max:40'],
            'name' => $this->group === 'phone_country_code'
                ? [
                    'required',
                    'string',
                    'max:12',
                    'regex:/^\+[0-9]{1,4}$/',
                    Rule::unique('master_records', 'name')
                        ->where(fn ($query) => $query
                            ->where('workspace_id', $workspaceId)
                            ->where('type', 'phone_country_code')
                            ->whereNull('deleted_at'))
                        ->ignore($this->editId),
                ]
                : ['required', 'string', 'max:255'],
            'productReferenceCode' => $this->group === 'product' ? ['nullable', 'string', 'max:80'] : ['nullable'],
            'productSupplierId' => $this->group === 'product' ? [
                'nullable',
                'integer',
                Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'supplier')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ] : ['nullable'],
            'productFormMainCategory' => $this->group === 'product' ? ['required', 'string', 'max:255'] : ['nullable'],
            'productSize' => $this->group === 'product' ? ['nullable', 'string', 'max:1200'] : ['nullable'],
            'productPriceTable' => $this->group === 'product' ? ['nullable', 'string', 'max:50000'] : ['nullable'],
            'productOptions' => $this->group === 'product' ? ['array', 'max:30'] : ['array'],
            'productOptions.*.key' => $this->group === 'product' ? ['required', 'string', 'max:80'] : ['nullable'],
            'productOptions.*.label' => $this->group === 'product' ? ['required', 'string', 'max:120'] : ['nullable'],
            'productOptions.*.extra_charge' => $this->group === 'product' ? ['nullable', 'numeric', 'min:0', 'max:999999.999999'] : ['nullable'],
            'productOptionUploads' => $this->group === 'product' ? ['array'] : ['array'],
            'productOptionUploads.*' => $this->group === 'product' ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'] : ['nullable'],
            'productShipmentUrgencies' => $this->group === 'product' ? ['array', 'max:20'] : ['array'],
            'productShipmentUrgencies.*.key' => $this->group === 'product' ? ['required', 'string', 'max:80'] : ['nullable'],
            'productShipmentUrgencies.*.shipment_urgency_id' => $this->group === 'product' ? ['required', 'integer', Rule::exists('master_records', 'id')->where(fn ($q) => $q->where('workspace_id', $workspaceId)->where('type', 'shipment_urgency')->whereNull('deleted_at'))] : ['nullable'],
            'productShipmentUrgencies.*.extra_charge' => $this->group === 'product' ? ['nullable', 'numeric', 'min:0', 'max:999999.999999'] : ['nullable'],
            'productSubcategory' => $this->group === 'product' ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'productClientAvailabilityMode' => $this->group === 'product' ? ['required', Rule::in(['all', 'specific'])] : ['nullable'],
            'productClientIds' => $this->group === 'product' && $this->productClientAvailabilityMode === 'specific' ? ['required', 'array', 'min:1'] : ['array'],
            'productClientIds.*' => ['integer', Rule::exists('clients', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'productTestCertificateNumber' => $this->group === 'product' ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'productCertificateUpload' => $this->group === 'product' ? AttachmentUpload::nullableRules(AttachmentUpload::PRODUCT_SUPPORTING, 10240) : ['nullable'],
            'productTemplateUpload' => $this->group === 'product' ? AttachmentUpload::nullableRules(AttachmentUpload::PRODUCT_SUPPORTING, 10240) : ['nullable'],
            'description' => ['nullable', 'string', 'max:5000'],
            'color' => in_array($this->group, MasterDataService::COLOR_TYPES, true)
                ? ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']
                : ['nullable'],
            'parentId' => match ($this->group) {
                'product' => [
                    'required',
                    'integer',
                    Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'product_category')
                    ->where('status', 'active')
                    ->whereNull('deleted_at'))
                ],
                'state' => ['required', 'integer', Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'country')
                    ->where('status', 'active')
                    ->whereNull('deleted_at'))],
                default => ['nullable'],
            },
            'status' => ['required', 'in:active,inactive'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:1000000'],
            'metadataJson' => ['nullable', 'string'],
            'workCalendarDayFrom' => $this->group === 'task_pack_work_calendar'
                ? ['required', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])]
                : ['nullable'],
            'workCalendarDayTo' => $this->group === 'task_pack_work_calendar'
                ? ['required', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])]
                : ['nullable'],
            'workCalendarTimeFrom' => $this->group === 'task_pack_work_calendar' ? ['required', 'date_format:H:i'] : ['nullable'],
            'workCalendarTimeTo' => $this->group === 'task_pack_work_calendar' ? ['required', 'date_format:H:i', 'after:workCalendarTimeFrom'] : ['nullable'],
            'autoInquiryStatus' => $this->group === 'inquiry_task_status'
                ? ['required', Rule::in(['To do', 'In Progress', 'Completed', 'Cancelled', '__task_status__'])]
                : ['nullable'],
            'requiresAttention' => $this->group === 'inquiry_task_status' ? ['boolean'] : ['nullable'],
            'orderTaskFlagId' => $this->group === 'order_task_status'
                ? ['nullable', 'integer', Rule::exists('master_records', 'id')->where(fn ($q) => $q->where('workspace_id', $workspaceId)->where('type', 'order_task_flag')->where('status', 'active')->whereNull('deleted_at'))]
                : ['nullable'],
            'orderFlagId' => $this->group === 'order_task_flag'
                ? ['required', 'integer', Rule::exists('master_records', 'id')->where(fn ($q) => $q->where('workspace_id', $workspaceId)->where('type', 'order_flag')->where('status', 'active')->whereNull('deleted_at'))]
                : ['nullable'],
            'productImage' => $this->group === 'product'
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
                : ['nullable'],
        ]);

        if ($this->group === 'product') {
            $taxonomy = app(\App\Services\ProductTaxonomyService::class);
            $main = $taxonomy->mainCategories(true)
                ->first(fn (MasterRecord $record) => mb_strtolower(trim($record->name)) === mb_strtolower(trim((string) $data['productFormMainCategory'])));
            $category = $taxonomy->productCategories(true)->firstWhere('id', (int) $data['parentId']);

            if (!$main) {
                throw ValidationException::withMessages([
                    'productFormMainCategory' => 'Select an active main category from Product Categories.',
                ]);
            }
            if (!$category || (int) ($taxonomy->mainCategoryFor($category)?->id ?? 0) !== (int) $main->id) {
                throw ValidationException::withMessages([
                    'parentId' => 'Select a product category that belongs to the selected main category.',
                ]);
            }

            $subcategoryName = trim((string) $data['productSubcategory']);
            if ($subcategoryName !== '') {
                $validSubcategory = $taxonomy->subcategories(true)->contains(
                    fn (MasterRecord $record) => (int) $record->parent_id === (int) $category->id
                        && mb_strtolower(trim($record->name)) === mb_strtolower($subcategoryName)
                );
                if (!$validSubcategory) {
                    throw ValidationException::withMessages([
                        'productSubcategory' => 'Select a subcategory that belongs to the selected product category.',
                    ]);
                }
            }

            // Persist the canonical main-category spelling even if an old Product
            // row was opened with legacy metadata/casing.
            $data['productFormMainCategory'] = $main->name;

            $shipmentUrgencyIds = collect($data['productShipmentUrgencies'] ?? [])
                ->pluck('shipment_urgency_id')
                ->map(fn ($value) => (int) $value)
                ->filter();
            if ($shipmentUrgencyIds->count() !== $shipmentUrgencyIds->unique()->count()) {
                throw ValidationException::withMessages([
                    'productShipmentUrgencies' => 'Each shipping urgency can only be added once to a product.',
                ]);
            }
        }

        $productPriceBreakpoints = [];
        $productRemoteSurchargeBreakpoints = [];
        if ($this->group === 'product' && trim((string) $data['productPriceTable']) !== '') {
            $parsedPriceTable = app(ProductPriceTableParser::class)->parseTable($data['productPriceTable']);
            $productPriceBreakpoints = $parsedPriceTable['price_breakpoints'];
            $productRemoteSurchargeBreakpoints = $parsedPriceTable['remote_surcharge_breakpoints'];
            if ($productPriceBreakpoints === []) {
                throw ValidationException::withMessages([
                    'productPriceTable' => 'Paste a valid price table containing quantity and product price values.',
                ]);
            }
        }

        $metadata = null;
        if (filled($data['metadataJson'])) {
            $metadata = json_decode($data['metadataJson'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($metadata)) {
                throw ValidationException::withMessages(['metadataJson' => 'Metadata must be valid JSON.']);
            }
        }

        if ($this->group === 'task_pack_work_calendar') {
            $metadata ??= [];
            $metadata['day_from'] = $data['workCalendarDayFrom'];
            $metadata['day_to'] = $data['workCalendarDayTo'];
            $metadata['time_from'] = $data['workCalendarTimeFrom'];
            $metadata['time_to'] = $data['workCalendarTimeTo'];
        }

        if ($this->group === 'inquiry_task_status') {
            $metadata ??= [];
            $metadata['auto_inquiry_status'] = $data['autoInquiryStatus'];
            $metadata['requires_attention'] = (bool) $data['requiresAttention'];
        }

        if ($this->group === 'order_task_status') {
            $metadata ??= [];
            if ($data['orderTaskFlagId']) {
                $metadata['order_task_flag_id'] = (int) $data['orderTaskFlagId'];
            } else {
                unset($metadata['order_task_flag_id']);
            }
        }

        if ($this->group === 'order_task_flag') {
            $metadata ??= [];
            $metadata['order_flag_id'] = (int) $data['orderFlagId'];
        }

        if ($this->group === 'product') {
            $metadata ??= [];
            $metadata['reference_code'] = trim((string) $data['productReferenceCode']);
            if (filled($data['productSupplierId'] ?? null)) {
                $defaultSupplierId = (int) $data['productSupplierId'];
                $metadata['supplier_id'] = $defaultSupplierId;
                unset($metadata['default_supplier_id']);
                $metadata['supplier_ids'] = collect((array) ($metadata['supplier_ids'] ?? []))
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn (int $id) => $id > 0)
                    ->prepend($defaultSupplierId)
                    ->unique()
                    ->values()
                    ->all();
            } else {
                // Clearing the default supplier must not destroy other supplier links.
                unset($metadata['supplier_id'], $metadata['default_supplier_id']);
            }
            $metadata['main_category'] = trim((string) $data['productFormMainCategory']);
            $metadata['product_size'] = trim((string) $data['productSize']) ?: null;
            if ($productPriceBreakpoints !== []) {
                $metadata['price_table_raw'] = trim((string) $data['productPriceTable']);
                $metadata['price_breakpoints'] = $productPriceBreakpoints;
                if ($productRemoteSurchargeBreakpoints !== []) {
                    $metadata['remote_surcharge_breakpoints'] = $productRemoteSurchargeBreakpoints;
                } else {
                    unset($metadata['remote_surcharge_breakpoints']);
                }
            } else {
                unset($metadata['price_table_raw'], $metadata['price_breakpoints'], $metadata['remote_surcharge_breakpoints']);
            }
            $metadata['sub_category'] = trim((string) $data['productSubcategory']) ?: null;
            unset($metadata['taxonomy_unassigned']);
            $metadata['test_certificate_number'] = trim((string) $data['productTestCertificateNumber']) ?: null;
            $metadata['client_availability'] = $data['productClientAvailabilityMode'];
            if ($data['productClientAvailabilityMode'] === 'specific') {
                $clients = Client::query()->whereIn('id', $data['productClientIds'])->orderBy('name')->get(['id', 'name', 'code']);
                $metadata['client_ids'] = $clients->pluck('id')->map(fn ($value) => (int) $value)->values()->all();
                $metadata['client_availability_labels'] = $clients->pluck('name')->values()->all();
                $metadata['client_codes'] = $clients->pluck('code')->filter()->values()->all();
            } else {
                unset($metadata['client_ids'], $metadata['client_availability_labels'], $metadata['client_codes']);
            }

            $shipmentUrgencyRows = collect($data['productShipmentUrgencies'] ?? []);
            if ($shipmentUrgencyRows->isNotEmpty()) {
                $shipmentUrgencies = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('shipment_urgency')
                    ->whereIn('id', $shipmentUrgencyRows->pluck('shipment_urgency_id')->map(fn ($value) => (int) $value)->all())
                    ->get(['id', 'code', 'name'])
                    ->keyBy('id');

                $metadata['shipment_urgency_options'] = $shipmentUrgencyRows->map(function (array $row) use ($shipmentUrgencies): array {
                    $urgencyId = (int) ($row['shipment_urgency_id'] ?? 0);
                    $urgency = $shipmentUrgencies->get($urgencyId);

                    return [
                        'key' => (string) ($row['key'] ?? Str::uuid()),
                        'shipment_urgency_id' => $urgencyId,
                        'shipment_urgency_code' => (string) ($urgency?->code ?? ''),
                        'shipment_urgency_name' => (string) ($urgency?->name ?? ''),
                        'extra_charge' => max(0, (float) ($row['extra_charge'] ?? 0)),
                    ];
                })->values()->all();
            } else {
                unset($metadata['shipment_urgency_options']);
            }
            // Remove the earlier incorrect shipment-method based product metadata.
            unset($metadata['shipping_options']);

            $metadata = array_filter($metadata, fn ($value, $key) => $key === 'reference_code' || ($value !== null && $value !== ''), ARRAY_FILTER_USE_BOTH);
        }

        if ($this->group === 'product' && $this->editId) {
            $existing = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->findOrFail($this->editId);
            foreach (['product_image_path', 'certificate_test_report_path', 'certificate_test_report', 'template_doc_path', 'template_doc'] as $key) {
                $value = data_get($existing->metadata, $key);
                if (filled($value)) {
                    $metadata ??= [];
                    $metadata[$key] = $value;
                }
            }
        }

        $wasCreating = !$this->editId;
        $record = app(\App\Actions\MasterData\SaveMasterRecordAction::class)->execute($this->group, [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'color' => in_array($this->group, MasterDataService::COLOR_TYPES, true) ? strtoupper($data['color']) : null,
            'parent_id' => in_array($this->group, ['product', 'state'], true) ? $data['parentId'] : null,
            'status' => $data['status'],
            'sort_order' => $data['sortOrder'],
            'metadata' => $metadata,
        ], $this->editId);

        if ($this->group === 'product') {
            try {
                $imageService = app(ProductImageService::class);
                if ($this->productImage) {
                    $record = $imageService->replace($record, $this->productImage);
                } elseif ($this->removeProductImage && data_get($record->metadata, 'product_image_path')) {
                    $record = $imageService->remove($record);
                }
            } catch (\Throwable $exception) {
                report($exception);
                // If the database row was just created, keep this modal attached
                // to that row so a retry updates it instead of creating a duplicate.
                if ($wasCreating) {
                    $this->editId = $record->id;
                    $this->existingProductImageUrl = $record->productImageUrl();
                }
                $this->addError('productImage', 'The product was saved, but its image could not be stored. Please try the image again.');
                return;
            }
        }

        if ($this->group === 'product') {
            try {
                $record = app(ProductOptionImageService::class)->sync(
                    $record,
                    $data['productOptions'] ?? [],
                    $this->productOptionUploads,
                );
            } catch (\Throwable $exception) {
                report($exception);
                if ($wasCreating) $this->editId = $record->id;
                $this->addError('productOptions', 'The product was saved, but an option image could not be stored. Please try the upload again.');
                return;
            }
        }

        if ($this->group === 'product') {
            try {
                $record = app(\App\Services\ProductDocumentService::class)->sync(
                    $record,
                    $this->productCertificateUpload,
                    $this->productTemplateUpload,
                    $this->removeProductCertificate,
                    $this->removeProductTemplate,
                );
            } catch (\Throwable $exception) {
                report($exception);
                if ($wasCreating) $this->editId = $record->id;
                $this->addError('productCertificateUpload', 'The product was saved, but a supporting document could not be stored. Please try the upload again.');
                return;
            }
        }

        $this->showModal = false;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCertificateUpload = null;
        $this->productTemplateUpload = null;
        $this->productOptions = [];
        $this->productOptionUploads = [];
        $this->productShipmentUrgencies = [];
        $this->productShipmentUrgencyPickerOpen = false;
        $this->productShipmentUrgencyPickerSelection = [];
        $this->removeProductCertificate = false;
        $this->removeProductTemplate = false;
        session()->flash('success', $this->group === 'product' ? 'Product saved.' : 'Master data saved.');
        app(\App\Services\NotificationService::class)->notifyUser(
            auth()->user(),
            'Master data updated',
            ($this->name ?: $this->code).' was saved in '.(MasterDataService::LABELS[$this->group] ?? 'Master Data').'.',
            'update',
            null,
            null,
            auth()->user(),
        );
    }
    public function updateColor(int $id, string $color): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        app(\App\Actions\MasterData\UpdateMasterColorAction::class)->execute($record->id, $color);
    }
    public function toggle(int $id): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        app(\App\Actions\MasterData\ToggleMasterRecordAction::class)->execute($record->id);
        session()->flash('success', 'Master data status updated.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data status updated', $record->name.' status was changed.', 'update', null, null, auth()->user());
    }
    public function deleteRecord(int $id): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        try {
            app(\App\Actions\MasterData\DeleteMasterRecordAction::class)->execute($record->id);
            $this->resetPage('masterPage');
            session()->flash('success', 'Master data record deleted.');
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data deleted', 'A master data record was deleted.', 'update', null, null, auth()->user());
        } catch (ValidationException $e) {
            $this->addError('record', collect($e->errors())->flatten()->first());
        }
    }
}
