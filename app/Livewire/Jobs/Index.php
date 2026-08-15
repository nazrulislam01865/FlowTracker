<?php

namespace App\Livewire\Jobs;

use App\Livewire\Concerns\HandlesInlineEdits;
use App\Livewire\Concerns\UsesPagePlaceholder;

use App\Models\Activity;
use App\Models\Document;
use App\Models\FlowJob;
use App\Models\FlowJobItem;
use App\Models\Invoice;
use App\Models\MasterRecord;
use App\Models\FlowTaskComment;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowPhase;
use App\Services\AccessControlService;
use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\JobService;
use App\Services\InquiryService;
use App\Services\MasterDataService;
use App\Services\OrderFinanceService;
use App\Services\ProductImageService;
use App\Services\TaskService;
use App\Services\WorkspaceSettingsService;
use App\Support\BoardLaneResolver;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use UsesPagePlaceholder;
    use HandlesInlineEdits;
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $phase = '';
    public string $health = '';
    public string $client = '';
    public string $owner = '';
    public string $assignee = '';
    public string $delivery = '';
    public string $invoice = '';
    public string $priorityFilter = '';
    public string $jobStatusFilter = '';
    public string $sort = 'updated_desc';
    public bool $showMoreFilters = false;
    public int $perPage = 25;
    public array $selectedJobIds = [];

    #[Url(as: 'open', history: true)]
    public ?int $selectedJobId = null;

    #[Url(as: 'task', history: true)]
    public ?int $selectedTaskId = null;

    #[Url(as: 'comment', history: true)]
    public ?string $focusComment = null;
    public bool $taskEditMode = false;
    public string $detailTab = 'overview';
    public string $inquirySearch = '';
    public ?int $selectedLinkInquiryId = null;
    public bool $showInquiryLinkConfirm = false;
    public bool $showInquiryUnlinkConfirm = false;

    public bool $showCreateInvoiceModal = false;
    public string $invoiceType = '';
    public string $invoiceCurrency = '';
    public string $invoiceIssueDate = '';
    public string $invoicePaymentTerms = '';
    public string $invoiceDueDate = '';
    public ?int $invoiceBillingContactId = null;
    public array $invoiceLineItems = [];
    public string $invoicePurchaseOrderReference = '';
    public string $invoiceNotes = 'Please include the invoice number with your payment.';
    public string $invoiceTaxRate = '0';
    public $invoiceSupportingDocument = null;
    public bool $invoiceEmailAfterCreation = false;

    public bool $showRecordPaymentModal = false;
    public ?int $paymentInvoiceId = null;
    public string $paymentDate = '';
    public string $paymentMethod = '';
    public string $paymentAmount = '';
    public string $paymentReference = '';
    public string $paymentReceivedAccount = '';
    public $paymentReceipt = null;
    public string $paymentNotes = '';
    public bool $paymentMarkInvoicePaid = true;

    public bool $showCollectionUpdateModal = false;
    public ?int $collectionOwnerId = null;
    public string $collectionFollowUpDate = '';
    public string $collectionNextFollowUpDate = '';
    public string $collectionNote = '';

    public array $expandedPhaseIds = [];
    public string $jobTaskSearch = '';
    public bool $showCreate = false;
    public bool $createCatalogReady = false;
    public bool $createAssignmentReady = false;
    public bool $createWorkflowReady = false;
    public int $createWorkflowSelectorVersion = 0;

    public string $jobTitle = '';
    public string $referenceNumber = '';
    public bool $isRepeatedOrder = false;
    public string $repeatedOrderNumber = '';
    public string $priority = 'Medium';
    public array $productionUrgencyIds = [];
    public array $shipmentUrgencyIds = [];
    public ?int $clientId = null;
    public ?int $workflowId = null;
    public ?int $workflowPhaseId = null;
    public ?int $ownerId = null;
    public ?int $coordinatorId = null;
    public string $deliveryDate = '';
    public string $estimatedDeliveryDate = '';
    public string $description = '';
    public array $jobItems = [];
    public string $createProductSearch = '';
    public string $createProductCategoryFilter = '';
    public bool $createProductShowAllResults = false;
    public bool $showCreateOrderProductModal = false;

    // Order Details > Add another product flow. Kept separate from Create Order
    // state so opening the detail picker never mutates the create-order draft.
    public bool $showAddJobProductForm = false;
    public string $jobProductSearch = '';
    public bool $jobProductShowAllResults = false;
    public ?int $jobProductSelectedId = null;
    public string $jobProductCategory = '';
    public string $jobProductQuantity = '1';
    public string $jobProductUnitPrice = '0.00';
    public string $newProductCode = '';
    public ?int $newProductCategoryId = null;
    public string $newProductCategorySearch = '';
    public string $newProductCategoryName = '';
    public string $newProductName = '';
    public $newProductImage = null;
    public array $jobAttachments = [];
    public array $jobDocumentUploads = [];
    public $jobRequiredDocumentUpload = null;
    public ?int $jobDocumentTaskId = null;
    public ?int $existingDocumentId = null;
    public bool $showDocumentPicker = false;
    public ?int $lastJobDocumentUploadId = null;
    public ?int $lastJobDocumentTaskId = null;

    public string $taskStatus = 'Ready';
    public ?int $taskAssigneeId = null;
    public int $taskProgress = 0;
    public bool $taskAttention = false;
    public string $taskAttentionReason = '';
    public string $taskComment = '';
    public string $newChecklistItem = '';
    public string $taskActivityTab = 'all';
    public int $taskActivityPage = 1;
    public string $jobComment = '';
    public string $jobActivityTab = 'all';
    public int $jobActivityPage = 1;
    public int $activityPerPage = 30;
    public array $taskDocumentUploads = [];
    /** Per-task temporary uploads used by the compact Order Overview taskflow. */
    public array $overviewTaskUploads = [];
    public bool $showOverviewTaskDocumentModal = false;
    public ?int $overviewTaskDocumentModalTaskId = null;
    public string $overviewTaskDocumentSource = 'upload';
    public $overviewTaskDocumentUpload = null;
    public ?int $overviewTaskExistingDocumentId = null;
    public string $overviewTaskDocumentNote = '';
    public ?int $overviewTaskLinkFormTaskId = null;
    public string $overviewTaskLinkUrl = '';
    public ?int $taskExistingDocumentId = null;
    public bool $showTaskDocumentPicker = false;
    public bool $showAddOrderTaskForm = false;
    public string $newOrderTaskName = '';
    public string $newOrderTaskDescription = '';
    public ?int $newOrderTaskPhaseId = null;
    public ?int $newOrderTaskAssigneeId = null;
    public string $newOrderTaskDueDate = '';

    public function mount(): void
    {
        $this->search = (string) request('search', '');
        $this->selectedJobId = request()->integer('open') ?: null;
        $this->selectedTaskId = request()->integer('task') ?: null;
        $requestedComment = trim((string) request('comment', $this->focusComment ?? ''));
        $this->focusComment = $requestedComment !== '' ? $requestedComment : null;
        $this->showCreate = request()->boolean('create');

        if ($this->showCreate) {
            abort_unless(auth()->user()->canModule('jobs', 'create'), 403);
            $this->selectedJobId = null;
            $this->selectedTaskId = null;
            $this->initializeCreateForm(request()->integer('client') ?: null);
            return;
        }

        $requestedClientFilter = request()->integer('client');
        if ($requestedClientFilter) {
            app(ClientService::class)
                ->referenceQuery(auth()->user(), 'jobs')
                ->where('is_active', true)
                ->findOrFail($requestedClientFilter);
            $this->client = (string) $requestedClientFilter;
        }

        if ($this->selectedTaskId) {
            $this->taskEditMode = true;
            $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
            $this->selectedJobId = (int) $task->flow_job_id;
            $this->loadTaskForm($this->selectedTaskId);
            $this->applyFocusedComment();
        }

        // A task deep-link is authorized by the task scope above. Do not run
        // the separate Job scope here as well: roles can legitimately have a
        // task in scope while the parent Order is outside their Job list scope.
        // Requiring both scopes caused valid tagged-comment links to 404.
        if ($this->selectedJobId && !$this->selectedTaskId) {
            $this->prepareSelectedJob($this->selectedJobId);
            $this->applyFocusedComment();
        }
    }

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

        if (in_array($property, ['clientId', 'ownerId', 'workflowId'], true)) {
            abort_unless($raw !== '' && ctype_digit($raw), 422, 'Please choose a valid option.');
            $id = (int) $raw;
            $type = match ($property) {
                'clientId' => 'clients',
                'ownerId' => 'users',
                'workflowId' => 'workflows',
            };

            $constraints = $property === 'workflowId' ? ['client_id' => $this->clientId] : [];
            $valid = $options->options($user, $type, 'create-job', '', $id, 20, $constraints)
                ->contains(fn ($item) => (string) ($item['id'] ?? '') === (string) $id);
            abort_unless($valid, 422, 'That option is no longer available.');

            $this->{$property} = $id;
            $this->resetValidation($property);

            if ($property === 'clientId') {
                // A Client change is a new Workflow context. Always discard the
                // previous Client's Workflow (including a manual override) and
                // resolve the default again from Workflow Setup. Do this even
                // before the progressively-loaded Workflow section is visible
                // so there is never a stale Client/Workflow pair in state.
                $this->applyClientWorkflowDefault($id);
            }

            if ($property === 'workflowId') {
                $this->setDefaultStartPhase();
                $this->resetValidation('workflowPhaseId');
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

    public function updatedJobItems(mixed $value, string $key): void
    {
        if (!$this->showCreate || !str_ends_with($key, '.category')) return;

        $index = (int) str($key)->before('.')->toString();
        if (!array_key_exists($index, $this->jobItems)) return;

        // A product belongs to the selected category. Clear any stale product
        // immediately so the remote selector cannot submit a value from the
        // previous category.
        $this->jobItems[$index]['product'] = '';
    }


    public function updatedCreateProductSearch(): void
    {
        $this->createProductShowAllResults = false;
    }

    public function updatedCreateProductCategoryFilter(): void
    {
        if (trim($this->createProductCategoryFilter) !== '') {
            abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        }
        $this->createProductShowAllResults = false;
        $this->dispatch('open-create-order-product-results');
    }

    public function showAllCreateProductResults(): void
    {
        $this->authorizeCreateOrderProducts();
        $this->createProductShowAllResults = true;
    }

    public function focusCreateProductSearch(): void
    {
        $this->authorizeCreateOrderProducts();
        $this->dispatch('focus-create-order-product-search');
    }

    public function selectCreateProduct(int $productId): void
    {
        $this->authorizeCreateOrderProducts();

        // Resolve from the canonical Product catalogue. Passing a Product
        // Category ID here now results in 404 instead of ever selecting it.
        $product = app(\App\Services\ProductCatalogService::class)
            ->findActiveProductOrFail($productId);

        // The selected row is always a Product master record. Category is
        // metadata copied from its parent (or a safe legacy fallback), never
        // the value used as the selected product itself.
        $productCategory = trim((string) ($product->parent?->name ?? ''));
        if ($productCategory === '') {
            $legacyDescription = trim((string) $product->description);
            $productCategory = trim(explode(' ·', $legacyDescription, 2)[0]);
        }
        $productCategory = $productCategory !== '' ? $productCategory : 'Uncategorized';

        $alreadySelected = collect($this->jobItems)->contains(
            fn (array $row): bool => (int) ($row['product_id'] ?? 0) === (int) $product->id
        );

        if (!$alreadySelected) {
            abort_if(count($this->jobItems) >= 25, 422, 'An Order can contain up to 25 products.');
            $this->jobItems[] = [
                'product_id' => (int) $product->id,
                'category' => $productCategory,
                'product' => (string) $product->name,
                'quantity' => 1000,
                'unit_price' => '',
                'notes' => '',
            ];
        }

        $this->resetValidation('jobItems');
        $this->dispatch('create-order-product-selected');
    }

    public function incrementCreateProductQuantity(int $index): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless(array_key_exists($index, $this->jobItems), 422);
        $current = max(1, (int) ($this->jobItems[$index]['quantity'] ?? 1));
        $this->jobItems[$index]['quantity'] = min(999999999, $current + 1);
        $this->resetValidation("jobItems.$index.quantity");
    }

    public function decrementCreateProductQuantity(int $index): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless(array_key_exists($index, $this->jobItems), 422);
        $current = max(1, (int) ($this->jobItems[$index]['quantity'] ?? 1));
        $this->jobItems[$index]['quantity'] = max(1, $current - 1);
        $this->resetValidation("jobItems.$index.quantity");
    }

    public function openCreateOrderProductModal(): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless(auth()->user()->canModule('catalog_products', 'create'), 403);
        abort_if(count($this->jobItems) >= 25, 422, 'An Order can contain up to 25 products.');

        $this->resetCreateOrderProductModal();
        $this->showCreateOrderProductModal = true;
    }

    public function openCreateOrderProductModalFromSearch(): void
    {
        $searchedName = trim($this->createProductSearch);
        $this->openCreateOrderProductModal();
        $this->newProductName = $searchedName;
    }

    public function closeCreateOrderProductModal(): void
    {
        $this->showCreateOrderProductModal = false;
        $this->resetCreateOrderProductModal();
    }

    public function selectCreateOrderProductCategory(int $categoryId): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless($this->showCreateOrderProductModal, 422);
        abort_unless(auth()->user()->canModule('catalog_products', 'create'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        if (!$this->newProductCodeReadyForCategory()) return;

        $category = MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product_category')
            ->active()
            ->findOrFail($categoryId);

        $this->newProductCategoryId = (int) $category->id;
        $this->newProductCategorySearch = (string) $category->name;
        $this->newProductCategoryName = '';
        $this->resetValidation(['newProductCategoryId', 'newProductCategoryName']);
        $this->dispatch('create-order-product-category-selected');
    }

    public function beginCreateOrderProductCategory(): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless($this->showCreateOrderProductModal, 422);
        abort_unless(auth()->user()->canModule('catalog_products', 'create'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'create'), 403);
        if (!$this->newProductCodeReadyForCategory()) return;
        $this->newProductCategoryName = trim($this->newProductCategorySearch);
        $this->resetValidation('newProductCategoryName');
    }

    public function cancelCreateOrderProductCategory(): void
    {
        $this->newProductCategoryName = '';
        $this->resetValidation('newProductCategoryName');
    }

    public function createCreateOrderProductCategory(): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless($this->showCreateOrderProductModal, 422);
        abort_unless(auth()->user()->canModule('catalog_products', 'create'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'create'), 403);
        if (!$this->newProductCodeReadyForCategory()) return;

        $name = trim($this->newProductCategoryName ?: $this->newProductCategorySearch);
        $this->newProductCategoryName = $name;
        $this->validate(['newProductCategoryName' => ['required', 'string', 'max:255']]);

        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $existing = MasterRecord::withTrashed()
            ->forWorkspace($workspaceId)
            ->ofType('product_category')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            if ($existing->trashed() || $existing->status !== 'active') {
                $this->addError('newProductCategoryName', 'A category with this name already exists but is inactive. Activate it from Product Categories first.');
                return;
            }

            $this->newProductCategoryId = (int) $existing->id;
            $this->newProductCategorySearch = (string) $existing->name;
            $this->newProductCategoryName = '';
            $this->resetValidation(['newProductCategoryId', 'newProductCategoryName']);
            $this->dispatch('create-order-product-category-created');
            return;
        }

        $category = $service->save('product_category', [
            'code' => $service->nextCode('product_category'),
            'name' => $name,
            'description' => null,
            'parent_id' => null,
            'status' => 'active',
            'sort_order' => ((int) MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->max('sort_order')) + 1,
            'metadata' => null,
        ]);

        $this->newProductCategoryId = (int) $category->id;
        $this->newProductCategorySearch = (string) $category->name;
        $this->newProductCategoryName = '';
        $this->resetValidation(['newProductCategoryId', 'newProductCategoryName']);
        $this->dispatch('create-order-product-category-created');
    }

    public function updatedNewProductCategorySearch(): void
    {
        if ($this->showCreateOrderProductModal) {
            abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        }
        $search = trim($this->newProductCategorySearch);
        if ($this->newProductCategoryId) {
            $selectedName = MasterRecord::query()
                ->forWorkspace(app(MasterDataService::class)->workspaceId())
                ->ofType('product_category')
                ->whereKey($this->newProductCategoryId)
                ->value('name');

            if (!$selectedName || mb_strtolower(trim((string) $selectedName)) !== mb_strtolower($search)) {
                $this->newProductCategoryId = null;
            }
        }
        $this->resetValidation('newProductCategoryId');
    }

    public function updatedNewProductCode(): void
    {
        $this->newProductCode = strtoupper(trim($this->newProductCode));
        $this->resetValidation('newProductCode');
    }

    private function newProductCodeReadyForCategory(): bool
    {
        $code = strtoupper(trim($this->newProductCode));
        $this->newProductCode = $code;

        if ($code === '') {
            $this->addError('newProductCode', 'Enter the SKU / product code first.');
            return false;
        }

        if (mb_strlen($code) > 40 || preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $code) !== 1) {
            $this->addError('newProductCode', 'Use letters, numbers, dots, dashes or underscores only. Maximum 40 characters.');
            return false;
        }

        $duplicate = MasterRecord::withTrashed()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product')
            ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)])
            ->first();

        if ($duplicate) {
            $this->addError('newProductCode', $duplicate->trashed()
                ? 'This product code is reserved by an archived product.'
                : 'This product code already exists.');
            return false;
        }

        $this->resetValidation('newProductCode');
        return true;
    }

    public function updatedNewProductImage(): void
    {
        abort_unless($this->showCreateOrderProductModal, 422);
        $this->validateOnly('newProductImage', [
            'newProductImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    public function selectDuplicateCreateOrderProduct(int $productId): void
    {
        $this->selectCreateProduct($productId);
        $this->showCreateOrderProductModal = false;
        $this->resetCreateOrderProductModal();
    }

    public function viewSimilarCreateProducts(): void
    {
        $this->createProductSearch = trim($this->newProductName);
        if ($this->newProductCategoryId) {
            $this->createProductCategoryFilter = (string) $this->newProductCategoryId;
        }
        $this->showCreateOrderProductModal = false;
        $this->resetCreateOrderProductModal(false);
        $this->dispatch('focus-create-order-product-search');
    }

    public function createAndAddOrderProduct(): void
    {
        $this->authorizeCreateOrderProducts();
        abort_unless($this->showCreateOrderProductModal, 422);
        abort_unless(auth()->user()->canModule('catalog_products', 'create'), 403);
        abort_unless(auth()->user()->canModule('product_categories', 'view'), 403);
        abort_if(count($this->jobItems) >= 25, 422, 'An Order can contain up to 25 products.');

        $this->newProductCode = strtoupper(trim($this->newProductCode));
        $this->newProductName = trim($this->newProductName);

        $data = $this->validate([
            'newProductCode' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/'],
            'newProductCategoryId' => ['required', 'integer'],
            'newProductName' => ['required', 'string', 'max:255'],
            'newProductImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $duplicate = MasterRecord::withTrashed()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->whereRaw('LOWER(code) = ?', [mb_strtolower($data['newProductCode'])])
            ->first();

        if ($duplicate) {
            $this->addError('newProductCode', $duplicate->trashed()
                ? 'This product code is reserved by an archived product.'
                : 'This product code already exists.');
            return;
        }

        $category = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_category')
            ->active()
            ->find((int) $data['newProductCategoryId']);
        if (!$category) {
            $this->addError('newProductCategoryId', 'Select a valid active product category.');
            return;
        }

        try {
            $product = $service->save('product', [
                'code' => $data['newProductCode'],
                'name' => $data['newProductName'],
                'description' => null,
                'parent_id' => $category->id,
                'status' => 'active',
                'sort_order' => ((int) MasterRecord::query()->forWorkspace($workspaceId)->ofType('product')->max('sort_order')) + 1,
                'metadata' => null,
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?: 'The product could not be created.';
            $this->addError('newProductCode', $message);
            return;
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('newProductCode', 'The product could not be created. Please try again.');
            return;
        }

        $imageStored = true;
        if ($this->newProductImage) {
            try {
                app(ProductImageService::class)->replace($product, $this->newProductImage);
            } catch (Throwable $exception) {
                report($exception);
                $imageStored = false;
            }
        }

        $this->selectCreateProduct((int) $product->id);
        $this->showCreateOrderProductModal = false;
        $this->resetCreateOrderProductModal();
        if (!$imageStored) {
            $this->dispatch('flowtrack-toast', message: 'Product created and selected. The image could not be stored.');
        }
    }

    private function resetCreateOrderProductModal(bool $resetSearchErrors = true): void
    {
        $this->newProductCode = '';
        $this->newProductCategoryId = null;
        $this->newProductCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->newProductName = '';
        $this->newProductImage = null;
        if ($resetSearchErrors) {
            $this->resetValidation([
                'newProductCode',
                'newProductCategoryId',
                'newProductCategoryName',
                'newProductName',
                'newProductImage',
            ]);
        }
    }
    public function updatedSearch(): void { $this->resetJobSelection(); }
    public function clearSearch(): void { $this->search = ''; $this->resetJobSelection(); }
    public function updatedPhase(): void { $this->resetJobSelection(); }
    public function updatedHealth(): void { $this->resetJobSelection(); }
    public function updatedClient(): void { $this->resetJobSelection(); }
    public function updatedOwner(): void { $this->resetJobSelection(); }
    public function updatedAssignee(): void { $this->resetJobSelection(); }
    public function updatedDelivery(): void { $this->resetJobSelection(); }
    public function updatedInvoice(): void { $this->resetJobSelection(); }
    public function updatedPriorityFilter(): void { $this->resetJobSelection(); }
    public function updatedJobStatusFilter(): void { $this->resetJobSelection(); }
    public function updatedSort(): void { $this->resetJobSelection(); }

    public function clearFilters(): void
    {
        $this->reset(['search','phase','health','client','owner','assignee','delivery','invoice','priorityFilter','jobStatusFilter']);
        $this->sort = 'updated_desc';
        $this->resetJobSelection();
    }

    public function clearFilter(string $filter): void
    {
        $allowed = ['search','phase','health','client','owner','assignee','delivery','invoice','priorityFilter','jobStatusFilter'];
        abort_unless(in_array($filter, $allowed, true), 422);
        $this->{$filter} = '';
        $this->resetJobSelection();
    }

    public function toggleMoreFilters(): void { $this->showMoreFilters = !$this->showMoreFilters; }

    public function toggleSelectAllJobs(): void
    {
        $ids = app(JobService::class)->filteredIds(auth()->user(), $this->jobFilters());

        if ($ids->isNotEmpty() && count($this->selectedJobIds) === $ids->count()) {
            $selected = collect($this->selectedJobIds)->map(fn ($id) => (int) $id)->sort()->values();
            if ($selected->all() === $ids->sort()->values()->all()) {
                $this->selectedJobIds = [];
                return;
            }
        }

        $this->selectedJobIds = $ids->all();
    }

    public function bulkUpdateJobs(string $action): void
    {
        abort_unless(in_array($action, ['deactivate','cancel','delete'], true), 422);

        $ids = collect($this->selectedJobIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) return;

        $service = app(JobService::class);
        $actor = auth()->user();
        $jobs = $service->visibleQuery($actor)->whereIn('id', $ids)->get();

        foreach ($jobs as $job) {
            match ($action) {
                'deactivate' => $service->deactivate($job, $actor),
                'cancel' => $service->cancel($job, $actor),
                'delete' => $service->delete($job, $actor),
            };
        }

        $this->resetJobSelection();
        session()->flash('success', match ($action) {
            'deactivate' => 'Selected Orders deactivated.',
            'cancel' => 'Selected Orders cancelled.',
            'delete' => 'Selected Orders deleted.',
        });
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
    public function addProductRow(): void { $this->focusCreateProductSearch(); }
    public function removeProductRow(int $index): void { if (!array_key_exists($index, $this->jobItems)) return; unset($this->jobItems[$index]); $this->jobItems = array_values($this->jobItems); $this->resetValidation('jobItems'); }

    public function openJob(int $id): void
    {
        $this->selectedJobId = $id;
        $this->selectedTaskId = null;
        $this->focusComment = null;
        $this->taskEditMode = false;
        $this->detailTab = 'overview';
        $this->inquirySearch = '';
        $this->selectedLinkInquiryId = null;
        $this->showInquiryLinkConfirm = false;
        $this->showInquiryUnlinkConfirm = false;
        $this->jobTaskSearch = '';
        $this->jobDocumentUploads = [];
        $this->overviewTaskUploads = [];
        $this->cancelAddOrderTask(false);
        $this->resetOverviewTaskResourceUi();
        $this->jobRequiredDocumentUpload = null;
        $this->jobDocumentTaskId = null;
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;
        $this->lastJobDocumentUploadId = null;
        $this->lastJobDocumentTaskId = null;
        $this->jobComment = '';
        $this->jobActivityTab = 'all';
        $this->jobActivityPage = 1;
        $this->closeFinanceModals();
        $this->prepareSelectedJob($id);
    }

    public function closeDrawer(): void
    {
        $this->selectedJobId = null;
        $this->selectedTaskId = null;
        $this->focusComment = null;
        $this->taskEditMode = false;
        $this->expandedPhaseIds = [];
        $this->inquirySearch = '';
        $this->selectedLinkInquiryId = null;
        $this->showInquiryLinkConfirm = false;
        $this->showInquiryUnlinkConfirm = false;
        $this->jobTaskSearch = '';
        $this->jobDocumentUploads = [];
        $this->overviewTaskUploads = [];
        $this->cancelAddOrderTask(false);
        $this->resetOverviewTaskResourceUi();
        $this->jobRequiredDocumentUpload = null;
        $this->jobDocumentTaskId = null;
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;
        $this->lastJobDocumentUploadId = null;
        $this->lastJobDocumentTaskId = null;
        $this->closeFinanceModals();

        $this->redirectRoute('jobs.index', navigate: true);
    }

    public function setDetailTab(string $tab): void
    {
        // The Order Details Documents/Workflow tabs remain intentionally muted.
        // Finance is a separate permission-controlled surface and does not change
        // any of the existing Overview or Inquiry behaviour.
        abort_unless(in_array($tab, ['overview','inquiry','finance'], true), 422);
        if ($tab === 'finance') {
            abort_unless(app(AccessControlService::class)->can(auth()->user(), 'finance', 'view'), 403);
        }
        $this->detailTab = $tab;
        if ($tab !== 'overview') {
            $this->cancelAddOrderTask(false);
            $this->resetOverviewTaskResourceUi();
        }
        if ($tab !== 'finance') $this->closeFinanceModals();
        $this->resetValidation(['inquiryLink', 'invoiceForm', 'paymentForm', 'collectionForm']);
    }

    public function openCreateInvoice(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'create'), 403);

        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        $job->load(['items', 'client.contacts']);

        $master = app(MasterDataService::class);
        $invoiceTypes = $master->active('invoice_type');
        $currencies = $master->active('currency');
        $paymentTerms = $master->active('payment_term');

        $this->invoiceType = (string) ($invoiceTypes->firstWhere('name', 'Final invoice')?->name ?: $invoiceTypes->first()?->name ?: '');
        $jobCurrency = strtoupper((string) ($job->currency ?: 'USD'));
        $currencyValues = $currencies->mapWithKeys(fn (MasterRecord $currency) => [$currency->id => $master->currencyValue($currency)]);
        $this->invoiceCurrency = (string) ($currencyValues->first(fn ($value) => $value === $jobCurrency) ?: $currencyValues->filter()->first() ?: '');
        $this->invoiceIssueDate = now()->toDateString();
        $this->invoicePaymentTerms = (string) ($paymentTerms->firstWhere('name', 'Net 15 days')?->name ?: $paymentTerms->first()?->name ?: '');
        $this->syncInvoiceDueDate();
        $this->invoiceBillingContactId = $job->client?->contacts?->firstWhere('is_primary', true)?->id
            ?: $job->client?->contacts?->first()?->id;
        $this->invoiceLineItems = app(OrderFinanceService::class)->defaultInvoiceItems($job);
        $this->invoicePurchaseOrderReference = (string) ($job->order_number ?? '');
        $this->invoiceNotes = 'Please include the invoice number with your payment.';
        $this->invoiceTaxRate = '0';
        $this->invoiceSupportingDocument = null;
        $this->invoiceEmailAfterCreation = false;
        $this->showCreateInvoiceModal = true;
        $this->resetValidation();
    }

    public function closeCreateInvoice(): void
    {
        $this->showCreateInvoiceModal = false;
        $this->invoiceSupportingDocument = null;
        $this->resetValidation();
    }

    public function clearInvoiceSupportingDocument(): void
    {
        $this->invoiceSupportingDocument = null;
        $this->resetValidation('invoiceSupportingDocument');
    }

    public function updatedInvoicePaymentTerms(): void
    {
        $this->syncInvoiceDueDate();
    }

    public function updatedInvoiceIssueDate(): void
    {
        $this->syncInvoiceDueDate();
    }

    public function addInvoiceLineItem(): void
    {
        abort_unless($this->showCreateInvoiceModal, 422);
        $this->invoiceLineItems[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeInvoiceLineItem(int $index): void
    {
        abort_unless($this->showCreateInvoiceModal, 422);
        if (count($this->invoiceLineItems) <= 1) return;
        unset($this->invoiceLineItems[$index]);
        $this->invoiceLineItems = array_values($this->invoiceLineItems);
        $this->resetValidation('invoiceLineItems');
    }

    public function createInvoice(bool $draft = false): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'create'), 403);
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        $job->load('client');

        $validated = $this->validate([
            'invoiceType' => ['required', Rule::in($this->financeMasterNames('invoice_type'))],
            'invoiceCurrency' => ['required', Rule::in($this->financeCurrencyCodes())],
            'invoicePaymentTerms' => ['required', Rule::in($this->financeMasterNames('payment_term'))],
            'invoiceIssueDate' => ['required', 'date'],
            'invoiceDueDate' => ['required', 'date', 'after_or_equal:invoiceIssueDate'],
            'invoiceBillingContactId' => ['nullable', 'integer', Rule::exists('client_contacts', 'id')->where('client_id', $job->client_id)],
            'invoicePurchaseOrderReference' => ['nullable', 'string', 'max:255'],
            'invoiceNotes' => ['nullable', 'string', 'max:5000'],
            'invoiceTaxRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'invoiceLineItems' => ['required', 'array', 'min:1'],
            'invoiceLineItems.*.description' => ['required', 'string', 'max:255'],
            'invoiceLineItems.*.quantity' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'invoiceLineItems.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'invoiceSupportingDocument' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,csv,txt'],
        ]);

        try {
            $invoice = app(OrderFinanceService::class)->createInvoice(
                $job,
                $user,
                [
                    'type' => $validated['invoiceType'],
                    'currency' => $validated['invoiceCurrency'],
                    'issue_date' => $validated['invoiceIssueDate'],
                    'due_date' => $validated['invoiceDueDate'],
                    'billing_contact_id' => $validated['invoiceBillingContactId'],
                    'purchase_order_reference' => $validated['invoicePurchaseOrderReference'] ?? '',
                    'notes' => $validated['invoiceNotes'] ?? '',
                    'tax_rate' => $validated['invoiceTaxRate'],
                ],
                $validated['invoiceLineItems'],
                $this->invoiceSupportingDocument,
                $draft,
            );
        } catch (Throwable $e) {
            $this->addError('invoiceForm', $e->getMessage());
            return;
        }

        $this->closeCreateInvoice();
        session()->flash('success', $draft ? 'Invoice saved as draft.' : 'Invoice created successfully.');
    }

    public function openRecordPayment(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'create'), 403);

        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        app(JobService::class)->loadVisibleDetailTab($job, $user, 'finance');
        $invoice = $job->invoices->first(fn ($candidate) => !in_array($candidate->status, ['draft', 'cancelled', 'paid'], true) && $candidate->balanceAmount() > 0);

        $master = app(MasterDataService::class);
        $paymentMethods = $master->active('payment_method');
        $receivedAccounts = $master->active('received_account');
        $preferredAccount = ($invoice?->currency ?: 'USD').' Operating Account';

        $this->paymentInvoiceId = $invoice?->id;
        $this->paymentDate = now()->toDateString();
        $this->paymentMethod = (string) ($paymentMethods->firstWhere('name', 'Bank transfer')?->name ?: $paymentMethods->first()?->name ?: '');
        $this->paymentAmount = $invoice ? number_format($invoice->balanceAmount(), 2, '.', '') : '';
        $this->paymentReference = '';
        $this->paymentReceivedAccount = (string) ($receivedAccounts->firstWhere('name', $preferredAccount)?->name ?: $receivedAccounts->first()?->name ?: '');
        $this->paymentReceipt = null;
        $this->paymentNotes = '';
        $this->paymentMarkInvoicePaid = true;
        $this->showRecordPaymentModal = true;
        $this->resetValidation();
    }

    public function updatedPaymentInvoiceId(): void
    {
        if (!$this->selectedJobId || !$this->paymentInvoiceId) return;
        $invoice = Invoice::query()->where('flow_job_id', $this->selectedJobId)->with('payments')->find($this->paymentInvoiceId);
        if ($invoice) {
            $this->paymentAmount = number_format($invoice->balanceAmount(), 2, '.', '');
            $receivedAccounts = app(MasterDataService::class)->active('received_account');
            $preferredAccount = ($invoice->currency ?: 'USD').' Operating Account';
            $this->paymentReceivedAccount = (string) ($receivedAccounts->firstWhere('name', $preferredAccount)?->name ?: $receivedAccounts->first()?->name ?: '');
        }
    }

    public function closeRecordPayment(): void
    {
        $this->showRecordPaymentModal = false;
        $this->paymentReceipt = null;
        $this->resetValidation();
    }

    public function clearPaymentReceipt(): void
    {
        $this->paymentReceipt = null;
        $this->resetValidation('paymentReceipt');
    }

    public function recordPayment(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        abort_unless(app(AccessControlService::class)->can($user, 'finance', 'create'), 403);
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);

        $validated = $this->validate([
            'paymentInvoiceId' => ['required', 'integer', Rule::exists('invoices', 'id')->where('flow_job_id', $job->id)],
            'paymentDate' => ['required', 'date'],
            'paymentMethod' => ['required', Rule::in($this->financeMasterNames('payment_method'))],
            'paymentAmount' => ['required', 'numeric', 'gt:0'],
            'paymentReference' => ['nullable', 'string', 'max:255'],
            'paymentReceivedAccount' => ['nullable', Rule::in($this->financeMasterNames('received_account'))],
            'paymentReceipt' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,csv,txt'],
            'paymentNotes' => ['nullable', 'string', 'max:3000'],
            'paymentMarkInvoicePaid' => ['boolean'],
        ]);

        try {
            app(OrderFinanceService::class)->recordPayment($job, $user, [
                'invoice_id' => $validated['paymentInvoiceId'],
                'payment_date' => $validated['paymentDate'],
                'method' => $validated['paymentMethod'],
                'amount' => $validated['paymentAmount'],
                'reference' => $validated['paymentReference'] ?? '',
                'received_account' => $validated['paymentReceivedAccount'] ?? '',
                'notes' => $validated['paymentNotes'] ?? '',
                'mark_invoice_paid' => (bool) ($validated['paymentMarkInvoicePaid'] ?? true),
            ], $this->paymentReceipt);
            $this->closeRecordPayment();
            session()->flash('success', 'Payment recorded successfully.');
        } catch (Throwable $e) {
            $this->addError('paymentForm', $e->getMessage());
        }
    }

    public function openCollectionUpdate(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canEditParentRecordModule($user, 'finance', $job), 403);
        app(JobService::class)->loadVisibleDetailTab($job, $user, 'finance');

        $this->collectionOwnerId = $job->collection?->collection_owner_id ?: $job->owner_id ?: $user->id;
        $this->collectionFollowUpDate = now()->toDateString();
        $this->collectionNextFollowUpDate = $job->collection?->next_follow_up_at?->format('Y-m-d') ?? '';
        $this->collectionNote = '';
        $this->showCollectionUpdateModal = true;
        $this->resetValidation();
    }

    public function closeCollectionUpdate(): void
    {
        $this->showCollectionUpdateModal = false;
        $this->resetValidation();
    }

    public function saveCollectionUpdate(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canEditParentRecordModule($user, 'finance', $job), 403);

        $validated = $this->validate([
            'collectionOwnerId' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
            'collectionFollowUpDate' => ['required', 'date'],
            'collectionNextFollowUpDate' => ['nullable', 'date', 'after_or_equal:collectionFollowUpDate'],
            'collectionNote' => ['required', 'string', 'max:3000'],
        ]);

        app(OrderFinanceService::class)->addCollectionUpdate($job, $user, [
            'collection_owner_id' => $validated['collectionOwnerId'],
            'follow_up_date' => $validated['collectionFollowUpDate'],
            'next_follow_up_at' => $validated['collectionNextFollowUpDate'] ?? '',
            'note' => $validated['collectionNote'],
        ]);

        $this->closeCollectionUpdate();
        session()->flash('success', 'Collection update saved.');
    }

    public function sendPaymentReminder(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'finance', 422);
        $user = auth()->user();
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canEditParentRecordModule($user, 'finance', $job), 403);
        app(JobService::class)->loadVisibleDetailTab($job, $user, 'finance');

        $summary = app(OrderFinanceService::class)->summary($job);
        $invoice = $job->invoices->first(fn ($candidate) => $candidate->status === 'overdue' && $candidate->balanceAmount() > 0)
            ?: $job->invoices->first(fn ($candidate) => !in_array($candidate->status, ['draft', 'cancelled', 'paid'], true) && $candidate->balanceAmount() > 0);
        $email = $invoice?->billing_contact_email
            ?: $job->client?->contacts?->firstWhere('is_primary', true)?->email
            ?: $job->client?->email;

        if (!$email) {
            $this->addError('collectionForm', 'No billing email is available for this client.');
            return;
        }

        try {
            Mail::raw(
                'Payment reminder for '.$job->displayOrderNumber().'. Outstanding balance: '.$job->currency.' '.number_format((float) $summary['outstanding'], 2).'.',
                fn ($message) => $message->to($email)->subject('Payment reminder · '.$job->displayOrderNumber())
            );
            app(OrderFinanceService::class)->addCollectionUpdate($job, $user, [
                'collection_owner_id' => $job->collection?->collection_owner_id ?: $job->owner_id ?: $user->id,
                'follow_up_date' => now()->toDateString(),
                'next_follow_up_at' => $job->collection?->next_follow_up_at?->format('Y-m-d') ?? '',
                'note' => 'Payment reminder sent to '.$email.'.',
            ], 'reminder');
            session()->flash('success', 'Payment reminder sent.');
        } catch (Throwable $e) {
            $this->addError('collectionForm', 'The reminder could not be sent: '.$e->getMessage());
        }
    }

    private function syncInvoiceDueDate(): void
    {
        if (!$this->invoiceIssueDate || trim($this->invoicePaymentTerms) === '') return;

        $days = $this->paymentTermDays($this->invoicePaymentTerms);
        if ($days === null) return;

        try {
            $this->invoiceDueDate = \Illuminate\Support\Carbon::parse($this->invoiceIssueDate)
                ->addDays($days)
                ->toDateString();
        } catch (Throwable) {
            // Validation will report an invalid date without mutating another field.
        }
    }

    /** @return array<int,string> */
    private function financeMasterNames(string $type): array
    {
        return app(MasterDataService::class)->active($type)
            ->pluck('name')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    /** @return array<int,string> */
    private function financeCurrencyCodes(): array
    {
        $master = app(MasterDataService::class);

        return $master->active('currency')
            ->map(fn (MasterRecord $currency) => $master->currencyValue($currency))
            ->filter(fn ($value) => (bool) preg_match('/^[A-Z]{3}$/', (string) $value))
            ->unique()
            ->values()
            ->all();
    }

    private function paymentTermDays(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') return null;

        $record = app(MasterDataService::class)->active('payment_term')
            ->first(fn ($term) => trim((string) $term->name) === $value || trim((string) $term->code) === $value);
        if (!$record) return null;

        $configured = data_get($record->metadata, 'days');
        if (is_numeric($configured)) return max(0, (int) $configured);
        if (preg_match('/(\d+)/', (string) $record->name, $matches)) return max(0, (int) $matches[1]);
        if (str_contains(strtolower((string) $record->name), 'receipt')) return 0;

        return null;
    }

    private function closeFinanceModals(): void
    {
        $this->showCreateInvoiceModal = false;
        $this->showRecordPaymentModal = false;
        $this->showCollectionUpdateModal = false;
        $this->invoiceSupportingDocument = null;
    }

    private function emailInvoice(Invoice $invoice): void
    {
        if (!$invoice->billing_contact_email) return;
        $job = $invoice->job()->with('client')->first();
        $subject = 'Invoice '.$invoice->invoice_number.' · '.($job?->displayOrderNumber() ?? 'Order');
        $html = '<p>Hello '.e($invoice->billing_contact_name ?: 'there').',</p>'
            .'<p>Invoice <strong>'.e($invoice->invoice_number).'</strong> has been created for '.e($job?->title ?: 'your order').'.</p>'
            .'<p><strong>Amount:</strong> '.e($invoice->currency).' '.number_format((float) $invoice->total, 2).'<br>'
            .'<strong>Due date:</strong> '.e($invoice->due_date?->format('M j, Y') ?: '—').'</p>'
            .'<p>'.nl2br(e((string) ($invoice->notes ?: 'Please include the invoice number with your payment.'))).'</p>';

        Mail::html($html, fn ($message) => $message->to($invoice->billing_contact_email)->subject($subject));
        $invoice->update(['emailed_at' => now()]);
    }

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
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canEditVisibleJob($user, $job), 403);

        $inquiry = app(InquiryService::class)->visibleQuery($user)
            ->with(['sourceOrder:id,source_inquiry_id', 'convertedJob:id'])
            ->findOrFail($inquiryId);

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
            $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
            app(JobService::class)->linkSourceInquiry($job, $this->selectedLinkInquiryId, $user);

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
            $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
            app(JobService::class)->unlinkSourceInquiry($job, $user);

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

    public function toggleJobPhase(int $phaseId): void
    {
        if (in_array($phaseId, $this->expandedPhaseIds, true)) {
            $this->expandedPhaseIds = array_values(array_filter($this->expandedPhaseIds, fn ($id) => (int) $id !== $phaseId));
        } else {
            $this->expandedPhaseIds[] = $phaseId;
            $this->expandedPhaseIds = array_values(array_unique(array_map('intval', $this->expandedPhaseIds)));
        }
    }

    public function expandAllJobPhases(): void
    {
        if (!$this->selectedJobId) return;

        $job = app(JobService::class)->visibleQuery(auth()->user())
            ->with(['workflow.phases:id,workflow_id'])
            ->select(['id', 'workflow_id'])
            ->findOrFail($this->selectedJobId);

        $this->expandedPhaseIds = $job->workflow->phases->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    public function collapseAllJobPhases(): void { $this->expandedPhaseIds = []; }

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

    private function persistJob(bool $draft): void
    {
        abort_unless($this->canUseCreateOrderProducts(auth()->user()), 403);

        if (!$this->createCatalogReady || !$this->createAssignmentReady || !$this->createWorkflowReady) {
            $this->addError('createLoading', 'Please wait for the remaining Create Order fields to finish loading.');
            return;
        }

        $data = $this->validate([
            'jobTitle' => ['required','string','max:255'],
            'referenceNumber' => ['nullable','string','max:255'],
            'isRepeatedOrder' => ['boolean'],
            'repeatedOrderNumber' => [Rule::requiredIf($this->isRepeatedOrder), 'nullable', 'string', 'max:255'],
            'productionUrgencyIds' => ['array', 'max:1'],
            'productionUrgencyIds.*' => [
                'integer',
                Rule::exists('master_records', 'id')->where(fn ($query) => $query
                    ->where('workspace_id', app(MasterDataService::class)->workspaceId())
                    ->where('type', 'production_urgency')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
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
            'workflowId' => ['required','exists:workflows,id'],
            'workflowPhaseId' => ['required','exists:workflow_phases,id'],
            'ownerId' => ['required','exists:users,id'],
            'coordinatorId' => ['nullable','exists:users,id'],
            'deliveryDate' => ['nullable','date'],
            'estimatedDeliveryDate' => ['nullable','date'],
            'description' => ['nullable','string'],
            'jobItems' => ['required','array','min:1','max:25'],
            'jobItems.*.product_id' => ['required','integer'],
            'jobItems.*.category' => ['required','string','max:255'],
            'jobItems.*.product' => ['required','string','max:255'],
            'jobItems.*.quantity' => ['required','integer','min:1','max:999999999'],
            'jobItems.*.unit_price' => ['nullable','numeric','min:0','max:999999999999.99'],
            'jobItems.*.notes' => ['nullable','string','max:2000'],
            'jobAttachments.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ], [
            'repeatedOrderNumber.required' => 'Enter the previous reference number for this repeated Order.',
            'jobItems.required' => 'Select at least one product for this Order.',
            'jobItems.min' => 'Select at least one product for this Order.',
        ]);

        if (count($this->jobAttachments) > 0) {
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

        $workflowAvailable = WorkflowTemplate::query()
            ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
            ->where('is_active', true)
            ->where('applies_to', 'orders')
            ->availableForOrderCreation((int) $data['clientId'])
            ->whereKey((int) $data['workflowId'])
            ->exists();

        if (!$workflowAvailable) {
            $this->addError('workflowId', 'That Workflow is not available for the selected client.');
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
            }
        }
        if ($catalogInvalid) return;

        $first = collect($data['jobItems'])->first();
        $job = app(JobService::class)->create([
            'order_number' => $data['referenceNumber'],
            'is_repeat_order' => (bool) $data['isRepeatedOrder'],
            'repeat_order_number' => $data['isRepeatedOrder'] ? trim((string) $data['repeatedOrderNumber']) : null,
            'title' => $data['jobTitle'],
            'product' => $first['product'] ?? null,
            'category' => $first['category'] ?? null,
            'quantity' => collect($data['jobItems'])->sum('quantity'),
            'items' => $data['jobItems'],
            // Keep the legacy Order priority stable for existing health/sort logic.
            // Create Order now captures production/shipment urgency separately.
            'priority' => 'Medium',
            'production_urgency_ids' => array_values(array_map('intval', $data['productionUrgencyIds'] ?? [])),
            'shipment_urgency_ids' => array_values(array_map('intval', $data['shipmentUrgencyIds'] ?? [])),
            'client_id' => $data['clientId'],
            'workflow_id' => $data['workflowId'],
            'workflow_phase_id' => $data['workflowPhaseId'],
            'owner_id' => $data['ownerId'],
            'coordinator_id' => $data['coordinatorId'] ?: $data['ownerId'],
            'delivery_date' => $data['deliveryDate'] ?: null,
            'estimated_delivery_date' => $data['estimatedDeliveryDate'] ?: null,
            'description' => $data['description'],
            'draft' => $draft,
        ], auth()->user());

        foreach ($this->jobAttachments as $upload) {
            app(\App\Services\DocumentService::class)->store($upload, [
                'flow_job_id' => $job->id,
                'client_id' => $job->client_id,
                'category' => 'Order attachment',
            ], auth()->user());
        }

        $this->showCreate = false;
        $this->resetCreateForm();
        $this->selectedJobId = $job->id;
        $this->detailTab = 'overview';
        $this->prepareSelectedJob($job->id);
        session()->flash('success', $draft ? 'Order draft saved.' : 'Order created and all configured Workflow Task Packs were loaded.');
    }

    #[Renderless]
    public function updateJobUrgencies(int $jobId, string $type, array $ids): array
    {
        $config = match ($type) {
            'production' => ['masterType' => 'production_urgency', 'field' => 'production_urgency_ids', 'label' => 'production urgency'],
            'shipment' => ['masterType' => 'shipment_urgency', 'field' => 'shipment_urgency_ids', 'label' => 'shipment urgency'],
            default => null,
        };

        if (!$config) {
            return ['ok' => false, 'message' => 'That urgency field is not available.'];
        }

        return $this->persistInlineEdit($config['label'], function () use ($jobId, $ids, $config) {
            $ids = collect($ids)->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->unique()->values()->all();
            abort_if(count($ids) > 1, 422, 'Select only one '.$config['label'].'.');
            $workspaceId = app(MasterDataService::class)->workspaceId();

            if ($ids) {
                $validIds = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType($config['masterType'])
                    ->active()
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                abort_if(count($validIds) !== count($ids), 422, 'One or more selected urgency options are no longer available.');
                $ids = $validIds;
            }

            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateUrgencies($job, $config['field'], $ids, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobOwner(int $jobId, mixed $ownerId): array
    {
        $owner = null;
        $result = $this->persistInlineEdit('Order owner', function () use ($jobId, $ownerId, &$owner) {
            $ownerId = $ownerId === '' ? null : (int) $ownerId;
            $owner = $ownerId ? User::where('is_active', true)->findOrFail($ownerId) : null;
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateOwner($job, $ownerId, auth()->user());
        });

        if ($result['ok'] ?? false) {
            $result['avatarUrl'] = $owner?->profileImageUrl();
        }

        return $result;
    }

    #[Renderless]
    public function updateJobCoordinator(int $jobId, mixed $coordinatorId): array
    {
        return $this->persistInlineEdit('Order coordinator', function () use ($jobId, $coordinatorId) {
            $coordinatorId = $coordinatorId === '' ? null : (int) $coordinatorId;
            if ($coordinatorId) User::where('is_active', true)->findOrFail($coordinatorId);
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateCoordinator($job, $coordinatorId, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobDeliveryDate(int $jobId, mixed $date): array
    {
        return $this->persistInlineEdit('delivery date', function () use ($jobId, $date) {
            $date = trim((string) $date);
            if ($date !== '') validator(['date' => $date], ['date' => ['date']])->validate();
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateDeliveryDate($job, $date ?: null, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobPriority(int $jobId, mixed $priority): array
    {
        return $this->persistInlineEdit('priority', function () use ($jobId, $priority) {
            $priority = trim((string) $priority);
            abort_if($priority === '', 422, 'Priority is required.');
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updatePriority($job, $priority, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobHealth(int $jobId, mixed $health): array
    {
        return $this->persistInlineEdit('Order health', function () use ($jobId, $health) {
            $health = trim((string) $health);
            abort_if($health === '', 422, 'Health is required.');
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            app(JobService::class)->updateHealth($job, $health, auth()->user());
        });
    }

    #[Renderless]
    public function updateJobTextField(int $jobId, string $field, mixed $value): array
    {
        $label = $field === 'title' ? 'Order name' : 'Order description';
        $updatedJob = null;

        $result = $this->persistInlineEdit($label, function () use ($jobId, $field, $value, &$updatedJob) {
            $job = app(JobService::class)->findVisible(auth()->user(), $jobId);
            $updatedJob = app(JobService::class)->updateTextField($job, $field, (string) $value, auth()->user());
        });

        if (($result['ok'] ?? false) && $updatedJob) {
            $result['value'] = (string) ($updatedJob->{$field} ?? '');

            if ($field === 'description') {
                $result['displayHtml'] = app(\App\Services\MentionService::class)
                    ->render($result['value']);
            }
        }

        return $result;
    }

    #[Renderless]
    public function updateJobItem(int $itemId, string $field, mixed $value): array
    {
        $label = match ($field) {
            'category_name' => 'product category',
            'product_name' => 'product',
            'quantity' => 'quantity',
            'unit_price' => 'unit price',
            'notes' => 'product notes',
            default => 'product detail',
        };

        return $this->persistInlineEdit($label, function () use ($itemId, $field, $value) {
            $user = auth()->user();
            abort_unless($this->selectedJobId, 422);

            $job = app(JobService::class)->findVisible($user, $this->selectedJobId);
            $item = FlowJobItem::where('flow_job_id', $job->id)->findOrFail($itemId);

            if ($field === 'category_name') {
                abort_unless(app(MasterDataService::class)->active('product_category')->contains('name', (string) $value), 422, 'Select a valid active product category.');
            }
            if ($field === 'product_name') {
                abort_if(blank($item->category_name), 422, 'Select a product category first.');
                $validProduct = app(\App\Services\FilterOptionService::class)
                    ->options($user, 'products', 'job-detail', '', (string) $value, 20, [
                        'category' => (string) $item->category_name,
                    ])
                    ->contains(fn ($option) => (string) ($option['id'] ?? '') === (string) $value);
                abort_unless($validProduct, 422, 'Select a valid active product for this category.');
            }

            app(JobService::class)->updateItem($job, $item, $field, $value, $user);
        });
    }

    public function openAddJobProductForm(int $jobId): void
    {
        $user = auth()->user();
        $job = app(JobService::class)->findVisible($user, $jobId);
        abort_unless(
            app(AccessControlService::class)->canEditVisibleJob($user, $job)
            && app(AccessControlService::class)->can($user, 'catalog_products', 'view')
            && app(AccessControlService::class)->can($user, 'catalog_products', 'create'),
            403
        );

        $this->resetValidation([
            'jobProductSelectedId', 'jobProductCategory', 'jobProductQuantity', 'jobProductUnitPrice',
        ]);
        $this->jobProductSearch = '';
        $this->jobProductShowAllResults = false;
        $this->jobProductSelectedId = null;
        $this->jobProductCategory = '';
        $this->jobProductQuantity = '1';
        $this->jobProductUnitPrice = '0.00';
        $this->showAddJobProductForm = true;
    }

    public function closeAddJobProductForm(): void
    {
        $this->showAddJobProductForm = false;
        $this->jobProductSearch = '';
        $this->jobProductShowAllResults = false;
        $this->jobProductSelectedId = null;
        $this->jobProductCategory = '';
        $this->jobProductQuantity = '1';
        $this->jobProductUnitPrice = '0.00';
        $this->resetValidation([
            'jobProductSelectedId', 'jobProductCategory', 'jobProductQuantity', 'jobProductUnitPrice',
        ]);
    }

    public function showAllJobProductResults(): void
    {
        abort_unless($this->showAddJobProductForm, 422);
        $this->jobProductShowAllResults = true;
    }

    public function selectJobProduct(int $productId): void
    {
        abort_unless($this->showAddJobProductForm && $this->selectedJobId, 422);
        $user = auth()->user();
        $job = app(JobService::class)->findVisible($user, $this->selectedJobId);
        abort_unless(
            app(AccessControlService::class)->canEditVisibleJob($user, $job)
            && app(AccessControlService::class)->can($user, 'catalog_products', 'view')
            && app(AccessControlService::class)->can($user, 'catalog_products', 'create'),
            403
        );

        $product = app(\App\Services\ProductCatalogService::class)->findActiveProductOrFail($productId);
        $category = trim((string) ($product->parent?->name ?? ''));
        if ($category === '') {
            $legacy = trim((string) $product->description);
            $category = trim(explode(' ·', $legacy, 2)[0]);
        }

        $this->jobProductSelectedId = (int) $product->id;
        $this->jobProductCategory = $category !== '' ? $category : 'Uncategorized';
        $this->jobProductSearch = (string) $product->name;
        $this->resetValidation(['jobProductSelectedId', 'jobProductCategory']);
    }

    public function saveJobProduct(int $jobId): void
    {
        abort_unless($this->showAddJobProductForm, 422);
        $user = auth()->user();
        $job = app(JobService::class)->findVisible($user, $jobId);
        abort_unless((int) $this->selectedJobId === (int) $job->id, 422);
        abort_unless(
            app(AccessControlService::class)->canEditVisibleJob($user, $job)
            && app(AccessControlService::class)->can($user, 'catalog_products', 'view')
            && app(AccessControlService::class)->can($user, 'catalog_products', 'create'),
            403
        );

        $data = $this->validate([
            'jobProductSelectedId' => ['required', 'integer'],
            'jobProductQuantity' => ['required', 'integer', 'min:1', 'max:999999999'],
            'jobProductUnitPrice' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
        ], [
            'jobProductSelectedId.required' => 'Select a product first.',
            'jobProductQuantity.required' => 'Enter a quantity.',
            'jobProductUnitPrice.required' => 'Enter a unit price.',
        ]);

        $product = app(\App\Services\ProductCatalogService::class)
            ->findActiveProductOrFail((int) $data['jobProductSelectedId']);
        $category = trim((string) ($product->parent?->name ?? ''));
        if ($category === '') {
            $legacy = trim((string) $product->description);
            $category = trim(explode(' ·', $legacy, 2)[0]);
        }
        $category = $category !== '' ? $category : 'Uncategorized';

        $alreadyAdded = $job->items()
            ->whereRaw('LOWER(product_name) = ?', [mb_strtolower((string) $product->name)])
            ->exists();
        if ($alreadyAdded) {
            $this->addError('jobProductSelectedId', 'This product is already added to the Order.');
            return;
        }

        app(JobService::class)->addItem(
            $job,
            $category,
            (string) $product->name,
            (int) $data['jobProductQuantity'],
            $user,
            (float) $data['jobProductUnitPrice'],
        );

        $this->closeAddJobProductForm();
    }

    public function addJobItem(int $jobId): void
    {
        $user = auth()->user();
        $job = app(JobService::class)->findVisible($user, $jobId);

        // Keep a single unfinished row at a time so repeated clicks cannot
        // accumulate invisible/partial product records.
        if ($job->items()->where(fn ($query) => $query
            ->whereNull('product_name')
            ->orWhere('product_name', '')
        )->exists()) {
            return;
        }

        // Add an intentionally blank draft row. The Job Details view renders
        // category, product, and quantity as editable controls immediately so
        // the user chooses the actual values instead of receiving master-data defaults.
        app(JobService::class)->addItem($job, '', '', 1, $user);
    }

    public function removeJobItem(int $itemId): void
    {
        abort_unless($this->selectedJobId, 422);
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $item = FlowJobItem::where('flow_job_id', $job->id)->findOrFail($itemId);
        app(JobService::class)->removeItem($job, $item, auth()->user());
    }

    #[Renderless]
    public function updateTaskAssigneeFromJob(int $taskId, mixed $assigneeId): array
    {
        $assignee = null;
        $result = $this->persistInlineEdit('task assignee', function () use ($taskId, $assigneeId, &$assignee) {
            abort_unless($this->selectedJobId, 422);
            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            $assigneeId = $assigneeId === '' ? null : (int) $assigneeId;
            $assignee = $assigneeId ? User::where('is_active', true)->findOrFail($assigneeId) : null;
            app(TaskService::class)->updateDetailField($task, 'assignee_id', $assigneeId, auth()->user());
        });

        if ($result['ok'] ?? false) {
            $result['avatarUrl'] = $assignee?->profileImageUrl();
        }

        return $result;
    }

    #[Renderless]
    public function updateTaskDueDateFromJob(int $taskId, mixed $date): array
    {
        return $this->persistInlineEdit('task due date', function () use ($taskId, $date) {
            abort_unless($this->selectedJobId, 422);
            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            $date = trim((string) $date);
            if ($date !== '') validator(['date' => $date], ['date' => ['date']])->validate();
            app(TaskService::class)->updateDueDate($task, $date ?: null, auth()->user());
        });
    }

    public function updatedJobRequiredDocumentUpload(): void
    {
        // Livewire's JavaScript upload API sets this property after the temporary
        // upload finishes. Permanent storage is deliberately handled by the
        // explicit persistJobRequiredDocumentUpload() action below so reaching
        // 100% can never be confused with a completed document save.
        $this->resetValidation(['jobRequiredDocumentUpload']);
    }

    public function persistJobRequiredDocumentUpload(): array
    {
        $this->resetValidation(['jobRequiredDocumentUpload', 'jobDocumentTaskId']);

        // A late Livewire upload callback must never save a new file after the
        // user has switched to "Choose existing" while that upload was in flight.
        if ($this->showDocumentPicker) {
            $this->jobRequiredDocumentUpload = null;
            return ['ok' => false, 'message' => 'Upload new is no longer the active document source.'];
        }

        if (!$this->jobRequiredDocumentUpload) {
            $message = 'The temporary upload was not received. Please choose the file again.';
            $this->addError('jobRequiredDocumentUpload', $message);
            return ['ok' => false, 'message' => $message];
        }

        if (!$this->selectedJobId) {
            $message = 'Open an Order before uploading a document.';
            $this->addError('jobRequiredDocumentUpload', $message);
            $this->jobRequiredDocumentUpload = null;
            return ['ok' => false, 'message' => $message];
        }

        try {
            $this->validate([
                'jobDocumentTaskId' => ['required','integer','exists:tasks,id'],
                'jobRequiredDocumentUpload' => ['required','file','max:20480','mimes:pdf,docx,xlsx,jpg,jpeg,png,zip'],
            ], [
                'jobDocumentTaskId.required' => 'Choose a document type before uploading a file.',
                'jobRequiredDocumentUpload.max' => 'The file is too large. The maximum size is 20 MB.',
                'jobRequiredDocumentUpload.mimes' => 'Use a PDF, DOCX, XLSX, JPG, PNG or ZIP file.',
            ]);

            $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
            $task = $job->tasks->firstWhere('id', (int) $this->jobDocumentTaskId);
            if (!$task || !($task->document_category_id || $task->setupTemplate?->document_category_id)) {
                $message = 'Choose a Task Pack document requirement for this Order.';
                $this->addError('jobDocumentTaskId', $message);
                $this->jobRequiredDocumentUpload = null;
                return ['ok' => false, 'message' => $message];
            }

            // The prototype is intentionally a one-document interaction. When a
            // user uploads again while the success state is still showing for the
            // same requirement, the new upload replaces that just-uploaded link.
            // Store the replacement first so a failed upload can never destroy the
            // document that is already attached.
            $replace = null;
            if ($this->lastJobDocumentUploadId && (int) $this->lastJobDocumentTaskId === (int) $task->id) {
                $replace = Document::where('flow_job_id', $job->id)
                    ->where('task_id', $task->id)
                    ->find($this->lastJobDocumentUploadId);
            }

            $document = app(DocumentService::class)->store($this->jobRequiredDocumentUpload, [
                'flow_job_id' => $job->id,
                'client_id' => $job->client_id,
                'task_id' => $task->id,
                'require_task_pack_requirement' => true,
            ], auth()->user());

            if ($replace && (int) $replace->id !== (int) $document->id) {
                app(DocumentService::class)->delete($replace, auth()->user());
            }

            $this->lastJobDocumentUploadId = (int) $document->id;
            $this->lastJobDocumentTaskId = (int) $task->id;
            $this->jobRequiredDocumentUpload = null;
            $this->resetValidation(['jobRequiredDocumentUpload', 'jobDocumentTaskId']);

            return [
                'ok' => true,
                'documentId' => (int) $document->id,
                'name' => (string) $document->name,
            ];
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $message = collect($exception->validator->errors()->all())->first()
                ?: 'The selected document could not be validated.';
            $this->jobRequiredDocumentUpload = null;
            $this->addError('jobRequiredDocumentUpload', $message);
            return ['ok' => false, 'message' => $message];
        } catch (Throwable $exception) {
            report($exception);
            $message = 'The document could not be saved. Please try again.';
            $this->jobRequiredDocumentUpload = null;
            $this->addError('jobRequiredDocumentUpload', $message);
            return ['ok' => false, 'message' => $message];
        }
    }

    public function clearJobRequiredDocumentUpload(): void
    {
        $this->jobRequiredDocumentUpload = null;
        $this->resetValidation(['jobRequiredDocumentUpload']);
    }

    public function removeLastJobDocumentUpload(): void
    {
        abort_unless(auth()->user()->canModule('documents','delete'), 403);
        abort_unless($this->selectedJobId && $this->lastJobDocumentUploadId, 422);

        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $document = Document::where('flow_job_id', $job->id)->findOrFail($this->lastJobDocumentUploadId);
        app(DocumentService::class)->delete($document, auth()->user());

        $this->lastJobDocumentUploadId = null;
        $this->lastJobDocumentTaskId = null;
        $this->jobRequiredDocumentUpload = null;
        $this->resetValidation(['jobRequiredDocumentUpload']);
    }

    public function setDocumentUploadMode(string $mode): void
    {
        abort_unless(in_array($mode, ['upload', 'existing'], true), 422);

        if ($mode === 'existing') {
            // Document source modes are mutually exclusive. Any pending new-file
            // selection must be discarded before the existing-document picker is
            // opened, otherwise both actions can remain active at the same time.
            $this->jobDocumentUploads = [];
            $this->jobRequiredDocumentUpload = null;
            $this->showDocumentPicker = true;
        } else {
            $this->existingDocumentId = null;
            $this->showDocumentPicker = false;
        }

        $this->resetValidation([
            'existingDocumentId',
            'jobRequiredDocumentUpload',
            'jobDocumentUploads',
            'jobDocumentUploads.*',
        ]);
    }

    public function updatedJobDocumentUploads(): void
    {
        // Temporary upload completion and permanent document persistence are two
        // separate steps in Livewire. Keep this hook limited to source-state
        // cleanup. The browser calls uploadJobOverviewDocuments() only after the
        // livewire-upload-finish event, which avoids racing the temporary upload.
        if (count($this->jobDocumentUploads) === 0) {
            return;
        }

        $this->showDocumentPicker = false;
        $this->existingDocumentId = null;
        $this->resetValidation(['existingDocumentId']);
    }

    /**
     * Livewire hook for the file button shown on each Order Overview task row.
     * Each task owns its temporary upload slot so several rows can be used
     * independently without changing the selected Task detail state.
     */
    public function openAddOrderTaskForm(): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);

        $user = auth()->user();
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canCreateJobTask($user, $job), 403);
        abort_if($job->completed_at || $job->status === 'Completed', 422, 'A completed Order cannot receive another task.');
        abort_if(in_array($job->status, JobService::INACTIVE_STATUSES, true), 422, 'An inactive Order cannot receive another task.');

        $this->newOrderTaskName = '';
        $this->newOrderTaskDescription = '';
        $this->newOrderTaskPhaseId = null;
        $this->newOrderTaskAssigneeId = $user->id;
        $this->newOrderTaskDueDate = app(WorkspaceSettingsService::class)->localToday()->addDays(3)->toDateString();
        $this->showAddOrderTaskForm = true;
        $this->resetValidation([
            'newOrderTaskName',
            'newOrderTaskDescription',
            'newOrderTaskPhaseId',
            'newOrderTaskAssigneeId',
            'newOrderTaskDueDate',
        ]);
    }

    public function cancelAddOrderTask(bool $resetValidation = true): void
    {
        $this->showAddOrderTaskForm = false;
        $this->newOrderTaskName = '';
        $this->newOrderTaskDescription = '';
        $this->newOrderTaskPhaseId = null;
        $this->newOrderTaskAssigneeId = null;
        $this->newOrderTaskDueDate = '';
        if ($resetValidation) {
            $this->resetValidation([
                'newOrderTaskName',
                'newOrderTaskDescription',
                'newOrderTaskPhaseId',
                'newOrderTaskAssigneeId',
                'newOrderTaskDueDate',
            ]);
        }
    }

    public function addOrderTask(?string $description = null): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);

        // The rich-text editor is intentionally isolated from Livewire DOM morphs
        // so typing, formatting, paste and screenshot insertion remain stable in
        // the dynamically opened Add Task form. Accept its canonical value at
        // submit time, then run the normal server-side validation/persistence.
        if ($description !== null) {
            $this->newOrderTaskDescription = $description;
        }

        $user = auth()->user();
        $job = app(JobService::class)->findVisibleBase($user, $this->selectedJobId);
        abort_unless(app(AccessControlService::class)->canCreateJobTask($user, $job), 403);

        $data = $this->validate([
            'newOrderTaskName' => ['required', 'string', 'max:255'],
            'newOrderTaskDescription' => ['nullable', 'string', 'max:60000'],
            'newOrderTaskPhaseId' => ['required', 'integer', 'exists:workflow_phases,id'],
            'newOrderTaskAssigneeId' => ['nullable', 'integer', 'exists:users,id'],
            'newOrderTaskDueDate' => ['nullable', 'date'],
        ]);

        if (filled($data['newOrderTaskAssigneeId'] ?? null)) {
            $allowedAssigneeIds = $this->userOptions($user)->pluck('id')->map(fn ($id) => (int) $id);
            abort_unless($allowedAssigneeIds->contains((int) $data['newOrderTaskAssigneeId']), 403);
        }

        app(JobService::class)->appendTask($job, [
            'title' => $data['newOrderTaskName'],
            'description' => $data['newOrderTaskDescription'] ?? null,
            'workflow_phase_id' => (int) $data['newOrderTaskPhaseId'],
            'assignee_id' => $data['newOrderTaskAssigneeId'] ?? null,
            'due_date' => $data['newOrderTaskDueDate'] ?? null,
        ], $user);

        $phaseId = (int) $data['newOrderTaskPhaseId'];
        if ($phaseId > 0) {
            $this->expandedPhaseIds = array_values(array_unique([
                ...array_map('intval', $this->expandedPhaseIds),
                $phaseId,
            ]));
        }

        $this->cancelAddOrderTask();
        session()->flash('success', 'Order task added.');
    }

    public function openOverviewTaskDocumentModal(int $taskId): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job', 'documentCategory', 'setupTemplate.documentCategory'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        $canCreate = auth()->user()->canModule('documents', 'create');
        $canLink = auth()->user()->canModule('documents', 'link');
        abort_unless($canCreate || $canLink, 403, 'Your role cannot add documents.');

        $this->showOverviewTaskDocumentModal = false;
        $this->overviewTaskLinkFormTaskId = null;
        $this->overviewTaskLinkUrl = '';
        $this->overviewTaskDocumentModalTaskId = $task->id;
        $this->overviewTaskDocumentSource = $canCreate ? 'upload' : 'existing';
        $this->overviewTaskDocumentUpload = null;
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->resetValidation([
            'overviewTaskDocumentUpload',
            'overviewTaskExistingDocumentId',
            'overviewTaskDocumentNote',
        ]);
        $this->showOverviewTaskDocumentModal = true;
    }

    public function closeOverviewTaskDocumentModal(): void
    {
        $this->showOverviewTaskDocumentModal = false;
        $this->overviewTaskDocumentModalTaskId = null;
        $this->overviewTaskDocumentSource = 'upload';
        $this->overviewTaskDocumentUpload = null;
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->resetValidation([
            'overviewTaskDocumentUpload',
            'overviewTaskExistingDocumentId',
            'overviewTaskDocumentNote',
        ]);
    }

    public function setOverviewTaskDocumentSource(string $source): void
    {
        abort_unless(in_array($source, ['upload', 'existing'], true), 422);
        if ($source === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
        }

        $this->overviewTaskDocumentSource = $source;
        $this->overviewTaskDocumentUpload = null;
        $this->overviewTaskExistingDocumentId = null;
        $this->resetValidation(['overviewTaskDocumentUpload', 'overviewTaskExistingDocumentId']);
    }

    public function saveOverviewTaskDocument(): void
    {
        abort_unless($this->selectedJobId && $this->overviewTaskDocumentModalTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['job', 'documentCategory', 'setupTemplate.documentCategory'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail((int) $this->overviewTaskDocumentModalTaskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        $this->validate([
            'overviewTaskDocumentSource' => ['required', Rule::in(['upload', 'existing'])],
            'overviewTaskDocumentNote' => ['nullable', 'string', 'max:2000'],
        ]);
        $note = trim($this->overviewTaskDocumentNote);
        $note = $note !== '' ? $note : null;
        $documentService = app(DocumentService::class);

        if ($this->overviewTaskDocumentSource === 'upload') {
            abort_unless(auth()->user()->canModule('documents', 'create'), 403);
            $this->validate([
                'overviewTaskDocumentUpload' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv,ai'],
            ], [
                'overviewTaskDocumentUpload.max' => 'The file is too large. Maximum file size is 20 MB.',
                'overviewTaskDocumentUpload.mimes' => 'Unsupported file type. Use PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT, CSV or AI.',
            ]);

            $storeData = [
                'flow_job_id' => $task->flow_job_id,
                'client_id' => $task->job?->client_id,
                'task_id' => $task->id,
                'note' => $note,
            ];
            if ($documentService->taskHasRequirement($task)) {
                $storeData['require_task_pack_requirement'] = true;
            } else {
                $storeData['category'] = 'Task attachment';
            }
            $documentService->store($this->overviewTaskDocumentUpload, $storeData, auth()->user());
        } else {
            abort_unless(auth()->user()->canModule('documents', 'link'), 403);
            $this->validate(['overviewTaskExistingDocumentId' => ['required', 'integer', 'exists:documents,id']]);
            $source = app(AccessControlService::class)
                ->applyDocumentScope(Document::query()->whereKey((int) $this->overviewTaskExistingDocumentId), auth()->user())
                ->firstOrFail();
            abort_unless((int) $source->client_id === (int) $task->job?->client_id, 403, 'The selected document does not belong to this client.');
            $documentService->linkExisting($source, $task, auth()->user(), true, $note);
        }

        $title = (string) $task->title;
        $this->closeOverviewTaskDocumentModal();
        session()->flash('success', 'Document added to '.$title.'.');
    }

    public function openOverviewTaskLinkForm(int $taskId): void
    {
        abort_unless($this->selectedJobId && $this->detailTab === 'overview', 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);
        $this->showOverviewTaskDocumentModal = false;
        $this->overviewTaskDocumentModalTaskId = null;
        $this->overviewTaskDocumentUpload = null;
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->overviewTaskLinkFormTaskId = $task->id;
        $this->overviewTaskLinkUrl = '';
        $this->resetValidation(['overviewTaskLinkUrl']);
    }

    public function cancelOverviewTaskLinkForm(): void
    {
        $this->overviewTaskLinkFormTaskId = null;
        $this->overviewTaskLinkUrl = '';
        $this->resetValidation(['overviewTaskLinkUrl']);
    }

    public function saveOverviewTaskLink(int $taskId): void
    {
        abort_unless((int) $this->overviewTaskLinkFormTaskId === $taskId, 422);
        $url = trim($this->overviewTaskLinkUrl);
        if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }
        $this->overviewTaskLinkUrl = $url;
        $this->validate([
            'overviewTaskLinkUrl' => ['required', 'string', 'max:2048', 'url'],
        ], [
            'overviewTaskLinkUrl.required' => 'Enter a link to add.',
            'overviewTaskLinkUrl.url' => 'Enter a valid website or file link.',
            'overviewTaskLinkUrl.max' => 'The link is too long.',
        ]);

        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        app(TaskService::class)->addExternalLink($task, $this->overviewTaskLinkUrl, auth()->user());
        $this->cancelOverviewTaskLinkForm();
        session()->flash('success', 'Link added to '.$task->title.'.');
    }

    public function deleteOverviewTaskLink(int $taskId, int $linkId): void
    {
        abort_unless($this->selectedJobId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);
        app(TaskService::class)->removeExternalLink($task, $linkId, auth()->user());
        session()->flash('success', 'Task link removed.');
    }

    public function updatedOverviewTaskUploads(mixed $value, string|int $key): void
    {
        if (!$value || !is_numeric($key)) return;

        $this->uploadOverviewTaskFile((int) $key);
    }

    public function uploadOverviewTaskFile(int $taskId): array
    {
        abort_unless($this->selectedJobId, 422);
        abort_unless(auth()->user()->canModule('documents', 'create'), 403);

        $property = 'overviewTaskUploads.'.$taskId;
        $this->resetValidation([$property]);
        $upload = $this->overviewTaskUploads[$taskId] ?? null;

        if (!$upload) {
            return ['ok' => false, 'message' => 'Choose a file to upload.'];
        }

        $validator = validator(['overviewTaskUploads' => [$taskId => $upload]], [
            $property => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ], [
            $property.'.required' => 'Choose a file to upload.',
            $property.'.max' => 'The file is too large. Maximum file size is 20 MB.',
            $property.'.mimes' => 'Unsupported file type. Use PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT or CSV.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->get($property) as $message) {
                $this->addError($property, $message);
            }
            return ['ok' => false, 'message' => $validator->errors()->first($property)];
        }

        $task = app(TaskService::class)
            ->visibleQuery(auth()->user())
            ->with(['job', 'documentCategory', 'setupTemplate.documentCategory'])
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($taskId);

        $documentService = app(DocumentService::class);
        $storeData = [
            'flow_job_id' => $task->flow_job_id,
            'client_id' => $task->job?->client_id,
            'task_id' => $task->id,
        ];

        if ($documentService->taskHasRequirement($task)) {
            // Preserve Task Pack requirement semantics so the Overview upload
            // immediately satisfies the same required-document gate used by
            // Workflow and Order Documents.
            $storeData['require_task_pack_requirement'] = true;
        } else {
            $storeData['category'] = 'Task attachment';
        }

        try {
            $documentService->store($upload, $storeData, auth()->user());
        } catch (\Throwable $e) {
            report($e);
            $message = 'FlowTrack could not store this task attachment. Please try again.';
            $this->addError($property, $message);
            return ['ok' => false, 'message' => $message];
        }

        unset($this->overviewTaskUploads[$taskId]);
        $this->resetValidation([$property]);
        session()->flash('success', 'Document uploaded and linked to '.$task->title.'.');

        return ['ok' => true];
    }

    public function uploadJobOverviewDocuments(): array
    {
        if ($this->selectedJobId && !$this->jobDocumentTaskId) {
            $this->setDefaultDocumentTask();
        }

        if (!$this->jobDocumentTaskId) {
            $message = 'No required document task is available for this Order.';
            $this->addError('jobDocumentUploads', $message);
            return ['ok' => false, 'message' => $message];
        }

        return $this->uploadJobDocuments();
    }

    public function removeJobDocumentUpload(int $index): void
    {
        if (! array_key_exists($index, $this->jobDocumentUploads)) return;

        unset($this->jobDocumentUploads[$index]);
        $this->jobDocumentUploads = array_values($this->jobDocumentUploads);
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*']);
    }

    public function clearJobDocumentUploads(): void
    {
        $this->jobDocumentUploads = [];
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*']);
    }

    public function uploadJobDocuments(): array
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        abort_unless($this->selectedJobId, 422);
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*', 'jobDocumentTaskId']);

        $validator = validator([
            'jobDocumentTaskId' => $this->jobDocumentTaskId,
            'jobDocumentUploads' => $this->jobDocumentUploads,
        ], [
            'jobDocumentTaskId' => ['required','integer','exists:tasks,id'],
            'jobDocumentUploads' => ['required','array','min:1'],
            'jobDocumentUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ], [
            'jobDocumentUploads.required' => 'Choose at least one file to upload.',
            'jobDocumentUploads.*.max' => 'The file is too large. Maximum file size is 20 MB.',
            'jobDocumentUploads.*.mimes' => 'Unsupported file type. Use PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT or CSV.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $messages) {
                foreach ($messages as $message) $this->addError($key, $message);
            }
            return ['ok' => false, 'message' => $validator->errors()->first()];
        }

        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $task = $job->tasks->firstWhere('id', (int) $this->jobDocumentTaskId);
        if (! $task || ! ($task->document_category_id || $task->setupTemplate?->document_category_id)) {
            $message = 'Select a Task Pack document requirement for this Order.';
            $this->addError('jobDocumentTaskId', $message);
            return ['ok' => false, 'message' => $message];
        }

        try {
            foreach ($this->jobDocumentUploads as $upload) {
                app(\App\Services\DocumentService::class)->store($upload, [
                    'flow_job_id' => $job->id,
                    'client_id' => $job->client_id,
                    'task_id' => $task->id,
                    'require_task_pack_requirement' => true,
                ], auth()->user());
            }
        } catch (\Throwable $e) {
            report($e);
            $message = 'FlowTrack could not store this document. Please try again.';
            $this->addError('jobDocumentUploads', $message);
            return ['ok' => false, 'message' => $message];
        }

        $this->jobDocumentUploads = [];
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*', 'jobDocumentTaskId']);

        $fresh = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $missing = \App\Support\JobDetailPresenter::requiredDocuments($fresh)
            ->first(fn ($requirement) => !$requirement->complete);
        $this->jobDocumentTaskId = $missing?->task?->id ?: $task->id;

        session()->flash('success', 'Document uploaded and linked to '.$task->title.'.');
        return ['ok' => true];
    }

    public function attachExistingDocument(): void
    {
        abort_unless(auth()->user()->canModule('documents','link'), 403);
        abort_unless($this->selectedJobId, 422);
        $this->validate([
            'jobDocumentTaskId' => ['required','integer','exists:tasks,id'],
            'existingDocumentId' => ['required','integer','exists:documents,id'],
        ]);

        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $task = $job->tasks->firstWhere('id', (int) $this->jobDocumentTaskId);
        if (! $task || ! ($task->document_category_id || $task->setupTemplate?->document_category_id)) {
            $this->addError('jobDocumentTaskId', 'Select a Task Pack document requirement for this Order.');
            return;
        }
        $source = Document::findOrFail((int) $this->existingDocumentId);
        if ((int) $source->client_id !== (int) $job->client_id) {
            $this->addError('existingDocumentId', 'The selected document does not belong to this client.');
            return;
        }
        $linked = app(\App\Services\DocumentService::class)->linkExisting($source, $task, auth()->user());
        $this->lastJobDocumentUploadId = (int) $linked->id;
        $this->lastJobDocumentTaskId = (int) $task->id;
        $this->jobDocumentUploads = [];
        $this->jobRequiredDocumentUpload = null;
        $this->existingDocumentId = null;
        $this->showDocumentPicker = false;
        $this->resetValidation(['jobDocumentUploads', 'jobDocumentUploads.*', 'jobRequiredDocumentUpload', 'existingDocumentId']);
        session()->flash('success', 'Existing document linked to the selected Task Pack task.');
    }

    public function deleteJobDocument(int $documentId): void
    {
        abort_unless(auth()->user()->canModule('documents','delete'), 403);
        abort_unless($this->selectedJobId, 422);
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        $document = Document::where('flow_job_id', $job->id)->findOrFail($documentId);
        app(\App\Services\DocumentService::class)->delete($document, auth()->user());
        if ((int) $this->lastJobDocumentUploadId === (int) $documentId) {
            $this->lastJobDocumentUploadId = null;
            $this->lastJobDocumentTaskId = null;
        }
    }

    public function toggleDocumentPicker(): void
    {
        $this->showDocumentPicker = !$this->showDocumentPicker;
    }

    #[Renderless]
    public function updateTaskStatusFromJob(int $taskId, mixed $status): array
    {
        return $this->persistInlineEdit('task status', function () use ($taskId, $status) {
            abort_unless($this->selectedJobId, 422);
            $status = trim((string) $status);
            abort_if($status === '', 422, 'Task status is required.');

            $task = Task::where('flow_job_id', $this->selectedJobId)->findOrFail($taskId);
            app(TaskService::class)->moveStatus($task, $status, auth()->user());
        });
    }

    public function completePhase(): void
    {
        if (!$this->selectedJobId) return;
        try {
            $job = app(JobService::class)->completePhase(app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId), auth()->user());
            $this->expandedPhaseIds = $job->phase ? [(int) $job->phase->id] : [];
            session()->flash('success', 'Phase completed and the next configured phase is active.');
        } catch (Throwable $e) {
            $this->addError('phaseCompletion', $e->getMessage());
        }
    }

    public function openTask(int $id): void
    {
        // Preserve the original task-detail behavior: opening a task normally
        // exposes inline editing when the current user has edit permission.
        $this->openTaskWithMode($id, true);
    }

    public function viewTask(int $id): void
    {
        // Explicit View from the overview action menu remains read-only.
        $this->openTaskWithMode($id, false);
    }

    public function editTask(int $id): void
    {
        $this->openTaskWithMode($id, true);
    }

    private function openTaskWithMode(int $id, bool $editMode): void
    {
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($id);
        if ($editMode) {
            abort_unless(app(AccessControlService::class)->canEditVisibleTask(auth()->user(), $task), 403);
        }

        $this->selectedJobId = (int) $task->flow_job_id;
        $this->selectedTaskId = $task->id;
        $this->focusComment = null;
        $this->taskEditMode = $editMode;
        $this->resetOverviewTaskResourceUi();
        $this->taskDocumentUploads = [];
        $this->taskExistingDocumentId = null;
        $this->showTaskDocumentPicker = false;
        $this->loadTaskForm($id);
    }

    public function deleteTaskFromJob(int $id): void
    {
        abort_unless($this->selectedJobId, 422);
        $actor = auth()->user();
        abort_unless(app(AccessControlService::class)->can($actor, 'tasks', 'delete'), 403);

        $task = app(TaskService::class)->visibleQuery($actor)
            ->where('flow_job_id', $this->selectedJobId)
            ->findOrFail($id);
        $job = app(JobService::class)->findVisible($actor, $this->selectedJobId);
        $title = (string) $task->title;
        $taskNumber = (string) ($task->task_number ?? '');

        $task->delete();
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.task_deleted',
            'description' => 'Task deleted: '.$title,
            'meta' => ['task_id' => $id, 'task_number' => $taskNumber, 'title' => $title],
        ]);
        app(JobService::class)->recalculateProgress($job->refresh());

        if ((int) $this->selectedTaskId === $id) {
            $this->closeTask();
        }
    }

    public function closeTask(): void { $this->selectedTaskId = null; $this->focusComment = null; $this->taskEditMode = false; $this->taskComment = ''; $this->newChecklistItem = ''; $this->taskActivityTab = 'all'; $this->taskActivityPage = 1; $this->taskDocumentUploads = []; $this->taskExistingDocumentId = null; $this->showTaskDocumentPicker = false; }
    public function markTaskComplete(): void
    {
        abort_unless($this->selectedTaskId, 422);
        $this->resetErrorBag('taskCompletion');

        $task = app(TaskService::class)->visibleQuery(auth()->user())
            ->with(['documents','documentCategory','setupTemplate.documentCategory'])
            ->findOrFail($this->selectedTaskId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditTask(auth()->user(), $task), 403);

        app(TaskService::class)->moveStatus($task, 'Completed', auth()->user());
        $this->loadTaskForm($task->id);
        session()->flash('success', 'Task marked complete.');
    }

    public function addTaskComment(): void
    {
        if (!$this->selectedTaskId || !app(\App\Services\RichTextService::class)->hasContent($this->taskComment)) return;
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        app(TaskService::class)->addComment($task, $this->taskComment, auth()->user());
        $this->taskComment = '';
        $this->taskActivityPage = 1;
    }

    public function setTaskActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all','comments','history'], true), 422);
        $this->taskActivityTab = $tab;
        $this->taskActivityPage = 1;
    }

    public function setTaskActivityPage(int $page): void
    {
        $this->taskActivityPage = max(1, $page);
    }

    #[Renderless]
    public function updateSelectedTaskField(string $field, mixed $value): array
    {
        $labels = [
            'title' => 'task name',
            'assignee_id' => 'task assignee',
            'status' => 'task status',
            'priority' => 'task priority',
            'start_date' => 'task start date',
            'due_date' => 'task due date',
            'description' => 'task description',
        ];

        $updatedTask = null;
        $result = $this->persistInlineEdit($labels[$field] ?? 'task field', function () use ($field, $value, &$updatedTask) {
            abort_unless($this->selectedTaskId, 422);
            $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
            if ($field === 'assignee_id' && filled($value)) User::where('is_active', true)->findOrFail((int) $value);
            if (in_array($field, ['start_date','due_date'], true) && filled($value)) validator(['date'=>$value], ['date'=>['date']])->validate();
            $updatedTask = app(TaskService::class)->updateDetailField($task, $field, $value, auth()->user());

            if ($field === 'status') $this->taskStatus = (string) $updatedTask->status;
            if ($field === 'assignee_id') $this->taskAssigneeId = $updatedTask->assignee_id ? (int) $updatedTask->assignee_id : null;
        });

        if ($field === 'status' && ($result['ok'] ?? false) && $updatedTask) {
            $timezone = app(WorkspaceSettingsService::class)->displayTimezone();
            $completedLocal = $updatedTask->completed_at?->copy()->timezone($timezone);
            $this->dispatch('task-completion-updated',
                completedDate: $completedLocal?->format('M j, Y') ?? '—',
                completedTime: $completedLocal?->format('g:i A') ?? ''
            );
        }

        if ($field === 'assignee_id' && ($result['ok'] ?? false) && $updatedTask) {
            $updatedTask->loadMissing('assignee:id,name,profile_image_path');
            $result['avatarUrl'] = $updatedTask->assignee?->profileImageUrl();
        }

        if ($field === 'description' && ($result['ok'] ?? false) && $updatedTask) {
            $result['value'] = (string) ($updatedTask->description ?? '');
            $result['displayHtml'] = app(\App\Services\MentionService::class)
                ->render($result['value']);
        }

        return $result;
    }

    public function addTaskChecklistItem(): void
    {
        abort_unless($this->selectedTaskId, 422);
        $this->validate(['newChecklistItem'=>['required','string','max:255']]);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        app(TaskService::class)->addChecklistItem($task, $this->newChecklistItem, auth()->user());
        $this->newChecklistItem = '';
    }

    public function toggleTaskChecklistItem(int $itemId, bool $completed): void
    {
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with('checklistItems')->findOrFail($this->selectedTaskId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditTask(auth()->user(), $task), 403, 'Only the assigned person or an authorised administrator can complete checklist items.');
        $item = $task->checklistItems->firstWhere('id', $itemId);
        abort_unless($item, 404);
        app(TaskService::class)->toggleChecklistItem($task, $item, $completed, auth()->user());
    }

    public function deleteTaskChecklistItem(int $itemId): void
    {
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with('checklistItems')->findOrFail($this->selectedTaskId);
        $item = $task->checklistItems->firstWhere('id', $itemId);
        abort_unless($item, 404);
        app(TaskService::class)->deleteChecklistItem($task, $item, auth()->user());
    }

    public function setJobActivityTab(string $tab): void
    {
        abort_unless(in_array($tab, ['all','comments','history'], true), 422);
        $this->jobActivityTab = $tab;
        $this->jobActivityPage = 1;
    }

    public function setJobActivityPage(int $page): void
    {
        $this->jobActivityPage = max(1, $page);
    }

    public function addJobComment(): void
    {
        abort_unless($this->selectedJobId, 422);
        $body = app(\App\Services\RichTextService::class)->normalize($this->jobComment, 5000, 'jobComment');
        if (!$body) return;
        $job = app(JobService::class)->findVisible(auth()->user(), $this->selectedJobId);
        abort_unless(app(\App\Services\AccessControlService::class)->canEditJob(auth()->user(), $job), 403);
        $actor = auth()->user();
        $mentionIds = app(\App\Services\MentionService::class)->userIdsFromText($body);
        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => 'job.comment',
            'description' => $body,
            'meta' => ['body' => $body, 'mention_user_ids' => $mentionIds],
        ]);
        app(\App\Services\NotificationService::class)->notifyJobParticipants(
            $job,
            'New comment on '.$job->displayOrderNumber(),
            $body,
            'comment',
            $actor,
            [],
            $mentionIds,
        );
        app(\App\Services\NotificationService::class)->notifyMentionedUsers(
            $mentionIds,
            $actor->name.' mentioned you in '.$job->displayOrderNumber(),
            $body,
            $job,
            null,
            $actor,
        );
        $this->jobComment = '';
        $this->jobActivityPage = 1;
    }

    public function updatedTaskDocumentUploads(): void
    {
        if (count($this->taskDocumentUploads) === 0) {
            return;
        }

        // Selecting a new file switches the source back to Upload new. Permanent
        // storage is triggered by the browser after livewire-upload-finish so the
        // temporary upload cannot race the save/link request.
        $this->showTaskDocumentPicker = false;
        $this->taskExistingDocumentId = null;
        $this->resetValidation(['taskExistingDocumentId']);
    }

    public function uploadSelectedTaskDocuments(): array
    {
        abort_unless(auth()->user()->canModule('documents','create'), 403);
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail($this->selectedTaskId);
        $this->resetValidation(['taskDocumentUploads', 'taskDocumentUploads.*']);

        $validator = validator(['taskDocumentUploads' => $this->taskDocumentUploads], [
            'taskDocumentUploads' => ['required','array','min:1'],
            'taskDocumentUploads.*' => ['file','max:20480','mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,txt,csv'],
        ], [
            'taskDocumentUploads.required' => 'Choose at least one file to upload.',
            'taskDocumentUploads.*.max' => 'The file is too large. Maximum file size is 20 MB.',
            'taskDocumentUploads.*.mimes' => 'Unsupported file type. Use PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, TXT or CSV.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $messages) {
                foreach ($messages as $message) $this->addError($key, $message);
            }
            return ['ok' => false, 'message' => $validator->errors()->first()];
        }

        try {
            foreach ($this->taskDocumentUploads as $upload) {
                app(\App\Services\DocumentService::class)->store($upload, [
                    'flow_job_id'=>$task->flow_job_id,
                    'client_id'=>$task->job?->client_id,
                    'task_id'=>$task->id,
                    'category'=>'Task attachment'
                ], auth()->user());
            }
        } catch (\Throwable $e) {
            report($e);
            $message = 'FlowTrack could not store this attachment. Please try again.';
            $this->addError('taskDocumentUploads', $message);
            return ['ok' => false, 'message' => $message];
        }

        $this->taskDocumentUploads = [];
        $this->taskExistingDocumentId = null;
        $this->showTaskDocumentPicker = false;
        $this->resetValidation(['taskDocumentUploads', 'taskDocumentUploads.*']);
        session()->flash('success', 'Attachment uploaded and linked to this Task Pack task.');
        return ['ok' => true];
    }

    public function attachExistingToSelectedTask(): void
    {
        abort_unless(auth()->user()->canModule('documents','link'), 403);
        abort_unless($this->selectedTaskId, 422);
        $this->validate(['taskExistingDocumentId'=>['required','integer','exists:documents,id']]);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with(['job','documentCategory','setupTemplate.documentCategory'])->findOrFail($this->selectedTaskId);
        $source = Document::findOrFail((int)$this->taskExistingDocumentId);
        abort_unless((int) $source->client_id === (int) $task->job?->client_id, 403, 'The selected document does not belong to this client.');
        app(\App\Services\DocumentService::class)->linkExisting($source, $task, auth()->user(), true);
        $this->taskDocumentUploads = [];
        $this->taskExistingDocumentId = null;
        $this->showTaskDocumentPicker = false;
        $this->resetValidation(['taskDocumentUploads', 'taskDocumentUploads.*', 'taskExistingDocumentId']);
        session()->flash('success', 'Stored document linked to this task.');
    }

    public function deleteSelectedTaskDocument(int $documentId): void
    {
        abort_unless(auth()->user()->canModule('documents','delete'), 403);
        abort_unless($this->selectedTaskId, 422);
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $document = Document::where('task_id',$task->id)->findOrFail($documentId);
        app(\App\Services\DocumentService::class)->delete($document, auth()->user());
    }

    public function toggleTaskDocumentPicker(): void
    {
        abort_unless(auth()->user()->canModule('documents', 'link'), 403);

        $opening = ! $this->showTaskDocumentPicker;
        if ($opening) {
            // Existing-document mode replaces Upload new; do not leave selected
            // temporary files and an Upload & link action active underneath it.
            $this->taskDocumentUploads = [];
            $this->resetValidation(['taskDocumentUploads', 'taskDocumentUploads.*']);
        } else {
            $this->taskExistingDocumentId = null;
            $this->resetValidation(['taskExistingDocumentId']);
        }

        $this->showTaskDocumentPicker = $opening;
    }

    public function setTaskFlag(string $flag): void
    {
        abort_unless($this->selectedTaskId, 422);

        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        $flag = trim($flag);

        if ($flag !== '') {
            $allowed = app(MasterDataService::class)->active('order_task_flag')->pluck('name')->map(fn ($name) => trim((string) $name));
            $currentLegacyFlag = $task->needs_attention ? trim((string) $task->attention_reason) : '';
            abort_unless($allowed->contains($flag) || ($currentLegacyFlag !== '' && $currentLegacyFlag === $flag), 422, 'Select a valid Task Flag.');
        }

        $updated = app(TaskService::class)->setAttentionFlag($task, $flag !== '' ? $flag : null, auth()->user());
        $this->taskAttention = (bool) $updated->needs_attention;
        $this->taskAttentionReason = (string) $updated->attention_reason;
    }

    public function toggleTaskAttention(): void
    {
        if (!$this->selectedTaskId) return;
        $task = app(TaskService::class)->visibleQuery(auth()->user())->with('orderTaskFlag:id,type,name,color,status,sort_order,metadata')->findOrFail($this->selectedTaskId);
        $flag = $task->needs_attention
            ? null
            : (app(\App\Services\TaskFlagService::class)->defaultActive()?->name ?: 'Management attention');
        $updated = app(TaskService::class)->setAttentionFlag($task, $flag, auth()->user());
        $this->taskAttention = (bool) $updated->needs_attention;
        $this->taskAttentionReason = (string) $updated->attention_reason;
    }

    public function saveTask(): void
    {
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($this->selectedTaskId);
        abort_unless(app(AccessControlService::class)->canEditTask(auth()->user(), $task), 403);
        $this->validate([
            'taskStatus' => ['required','string','max:50'],
            'taskAssigneeId' => ['nullable','exists:users,id'],
            'taskProgress' => ['required','integer','between:0,100'],
            'taskAttentionReason' => ['nullable','string','max:1000'],
        ]);
        app(TaskService::class)->update($task, [
            'status' => $this->taskStatus,
            'assignee_id' => $this->taskAssigneeId,
            'progress' => $this->taskProgress,
            'attention_reason' => $this->taskAttentionReason,
        ], auth()->user());
        if (trim($this->taskComment) !== '') app(TaskService::class)->addComment($task, $this->taskComment, auth()->user());
        session()->flash('success', 'Task update saved.');
        $this->selectedTaskId = null;
        $this->taskComment = '';
    }

    private function loadTaskForm(int $id): void
    {
        $task = app(TaskService::class)->visibleQuery(auth()->user())->findOrFail($id);
        $this->taskStatus = $task->status;
        $this->taskAssigneeId = $task->assignee_id;
        $this->taskProgress = $task->progress;
        $this->taskAttention = $task->needs_attention;
        $this->taskAttentionReason = (string) $task->attention_reason;
        $this->taskComment = '';
        $this->newChecklistItem = '';
        $this->taskActivityTab = 'all';
        $this->taskActivityPage = 1;
    }

    private function applyFocusedComment(): void
    {
        $focus = trim((string) $this->focusComment);
        if ($focus === '') return;

        if ($this->selectedTaskId && preg_match('/^task-(\d+)$/', $focus, $matches) === 1) {
            $comment = FlowTaskComment::query()
                ->where('flow_task_id', $this->selectedTaskId)
                ->find((int) $matches[1]);

            if (!$comment) {
                $this->focusComment = null;
                return;
            }

            $newerCount = FlowTaskComment::query()
                ->where('flow_task_id', $this->selectedTaskId)
                ->where(function ($query) use ($comment): void {
                    $query->where('created_at', '>', $comment->created_at)
                        ->orWhere(function ($sameTime) use ($comment): void {
                            $sameTime->where('created_at', $comment->created_at)->where('id', '>', $comment->id);
                        });
                })
                ->count();

            $this->taskActivityTab = 'comments';
            $this->taskActivityPage = intdiv($newerCount, 30) + 1;
            return;
        }

        if ($this->selectedJobId && !$this->selectedTaskId && preg_match('/^job-(\d+)$/', $focus, $matches) === 1) {
            $activity = Activity::query()
                ->where('subject_type', FlowJob::class)
                ->where('subject_id', $this->selectedJobId)
                ->where('event', 'job.comment')
                ->find((int) $matches[1]);

            if (!$activity) {
                $this->focusComment = null;
                return;
            }

            $newerCount = Activity::query()
                ->where('subject_type', FlowJob::class)
                ->where('subject_id', $this->selectedJobId)
                ->where('event', 'job.comment')
                ->where(function ($query) use ($activity): void {
                    $query->where('created_at', '>', $activity->created_at)
                        ->orWhere(function ($sameTime) use ($activity): void {
                            $sameTime->where('created_at', $activity->created_at)->where('id', '>', $activity->id);
                        });
                })
                ->count();

            $this->detailTab = 'overview';
            $this->jobActivityTab = 'comments';
            $this->jobActivityPage = intdiv($newerCount, 10) + 1;
            return;
        }

        $this->focusComment = null;
    }

    private function preferredCreateOrderWorkflowId(?int $clientId): ?int
    {
        $preferred = WorkflowTemplate::query()
            ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
            ->where('is_active', true)
            ->where('applies_to', 'orders')
            ->availableForOrderCreation($clientId)
            // Create Order must never inherit an Inquiry workflow. Prefer a
            // client-specific Order workflow, then the normal all-client Order
            // workflow. Defaults win inside each availability tier.
            ->orderByRaw("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END")
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->value('id');

        return $preferred ? (int) $preferred : null;
    }

    private function createOrderWorkflowAvailableForClient(int $workflowId, ?int $clientId): bool
    {
        return WorkflowTemplate::query()
            ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
            ->where('is_active', true)
            ->where('applies_to', 'orders')
            ->availableForOrderCreation($clientId)
            ->whereKey($workflowId)
            ->exists();
    }

    private function applyClientWorkflowDefault(?int $clientId): void
    {
        // Clear first so both Livewire and Alpine cannot temporarily retain the
        // previous Client's selection while the new default is being resolved.
        $this->workflowId = null;
        $this->workflowPhaseId = null;

        if ($clientId) {
            $this->workflowId = $this->preferredCreateOrderWorkflowId($clientId);
            $this->setDefaultStartPhase();
        }

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

        $this->workflowPhaseId = WorkflowPhase::query()
            ->where(function ($query): void {
                $query->where('workflow_template_id', $this->workflowId)
                    ->orWhere('workflow_id', $this->workflowId);
            })
            ->where('is_active', true)
            ->where('allow_job_start', true)
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
        $this->jobItems = [];
    }

    private function resetCreateForm(): void
    {
        $this->resetValidation();
        $this->reset([
            'jobTitle',
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
            'jobItems',
            'createProductSearch',
            'createProductCategoryFilter',
            'createProductShowAllResults',
            'showCreateOrderProductModal',
            'newProductCode',
            'newProductCategoryId',
            'newProductCategorySearch',
            'newProductCategoryName',
            'newProductName',
            'newProductImage',
            'jobAttachments',
            'createCatalogReady',
            'createAssignmentReady',
            'createWorkflowReady',
            'createWorkflowSelectorVersion',
        ]);
    }

    private function prepareSelectedJob(int $id): void
    {
        $job = app(JobService::class)->visibleQuery(auth()->user())
            ->with(['workflow.phases:id,workflow_id'])
            ->select(['id', 'workflow_id', 'workflow_phase_id'])
            ->findOrFail($id);

        if (!$this->expandedPhaseIds) {
            $phaseIds = $job->workflow?->phases?->pluck('id') ?? collect();
            $this->expandedPhaseIds = $phaseIds
                ->map(fn ($phaseId) => (int) $phaseId)
                ->values()
                ->all();
        }
    }

    private function setDefaultDocumentTask(?FlowJob $job = null): void
    {
        if (!$this->selectedJobId && !$job) return;

        if (!$job) {
            $user = auth()->user();
            $service = app(JobService::class);
            $job = $service->findVisibleBase($user, $this->selectedJobId);
            $service->loadVisibleDetailTab($job, $user, 'documents');
        } elseif (!$job->relationLoaded('tasks')) {
            app(JobService::class)->loadVisibleDetailTab($job, auth()->user(), 'documents');
        }

        $valid = $job->tasks->first(fn ($task) => (int) $task->id === (int) $this->jobDocumentTaskId && ($task->document_category_id || $task->setupTemplate?->document_category_id));
        if ($valid) return;

        $task = $job->tasks
            ->filter(fn ($task) => $task->document_category_id || $task->setupTemplate?->document_category_id)
            ->sortBy(fn ($task) => [
                (int) ($task->workflow_phase_id === $job->workflow_phase_id ? 0 : 1),
                (int) ($task->phase?->sequence ?? 999),
                (int) ($task->setupTemplate?->sort_order ?? 999),
            ])->first();
        $this->jobDocumentTaskId = $task?->id;
    }

    #[On('flowtrack-notification')]
    public function refreshRealtime(): void
    {
        // Re-render open Job/Task details when another permitted user updates them.
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->showCreate) {
            return view('livewire.jobs.index', $this->createPageData($user));
        }

        if ($this->selectedTaskId) {
            return view('livewire.jobs.index', $this->taskPageData($user));
        }

        if ($this->selectedJobId) {
            return view('livewire.jobs.index', $this->jobPageData($user));
        }

        return view('livewire.jobs.index', $this->jobsTableData($user));
    }

    private function createPageData(User $user): array
    {
        $master = app(MasterDataService::class);
        $options = app(\App\Services\FilterOptionService::class);

        // Create Job is a separate render branch. Keep only the selected
        // records needed to render dependent fields; large option lists are
        // loaded by the shared remote selector only when the user opens them.
        $clients = $this->clientId
            ? app(ClientService::class)
                ->referenceQuery($user, 'create-job')
                ->where('is_active', true)
                ->whereKey($this->clientId)
                ->get(['id', 'name', 'contact_name'])
            : collect();

        // Render Create Order from workflow_templates (the setup source of
        // truth), not the legacy workflows mirror. This makes Workflow name
        // edits and client assignments appear immediately, including NEP.
        $workflows = $this->createWorkflowReady && $this->workflowId
            ? WorkflowTemplate::query()
                ->with('phases.taskPack.templates')
                ->where('workspace_id', app(\App\Services\WorkflowService::class)->workspaceId())
                ->where('is_active', true)
                ->where('applies_to', 'orders')
                ->availableForOrderCreation($this->clientId)
                ->whereKey($this->workflowId)
                ->get()
            : collect();

        $workspaceId = $master->workspaceId();
        $canUseOrderProductSelector = $this->canUseCreateOrderProducts($user);
        $canViewProductCategories = $user->canModule('product_categories', 'view');
        $productCategories = collect();
        $productSearchResults = collect();
        $selectedProductDetails = collect();
        $activeProductCount = 0;
        $productResultTotal = 0;

        if ($this->createCatalogReady && $canUseOrderProductSelector) {
            if ($canViewProductCategories) {
                $productCategories = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_category')
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'code']);
            }

            // Canonical Product catalogue source. This service can only return
            // master_records.type = 'product'. Product Categories are loaded
            // separately above and are permitted only as filters/metadata.
            $catalog = app(\App\Services\ProductCatalogService::class);
            $activeProductCount = $catalog->activeCount();

            $search = trim($this->createProductSearch);
            $categoryFilterId = $canViewProductCategories && ctype_digit(trim($this->createProductCategoryFilter))
                ? (int) $this->createProductCategoryFilter
                : 0;
            $productResultTotal = $catalog->orderSearchCount($search, $categoryFilterId ?: null);
            // With a small catalogue, show every Product record immediately so the
            // selector cannot be mistaken for a category suggestion list. Large
            // catalogues keep the prototype's Top matches + View all behaviour.
            $resultLimit = $this->createProductShowAllResults || $productResultTotal <= 20 ? 20 : 3;
            $productSearchResults = $catalog->searchForOrderCreation($search, $categoryFilterId ?: null, $resultLimit);

            $selectedProductDetails = $catalog->selectedProducts(
                collect($this->jobItems)->pluck('product_id')
            );
        }

        $duplicateProduct = null;
        $newProductCategoryMatches = collect();
        $newProductSimilarCategories = collect();
        $newProductSimilarProducts = collect();
        $newProductSelectedCategory = null;
        $newProductHasExactCategory = false;
        $newProductImagePreview = null;

        if ($canUseOrderProductSelector && $this->showCreateOrderProductModal) {
            $code = trim($this->newProductCode);
            if ($code !== '') {
                $duplicateProduct = MasterRecord::withTrashed()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->with('parent:id,name,status')
                    ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)])
                    ->first(['id', 'type', 'parent_id', 'name', 'code', 'metadata', 'status']);
            }

            $categorySearch = trim($this->newProductCategorySearch);
            if ($canViewProductCategories) {
                $newProductCategoryMatches = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_category')
                ->active()
                ->when($categorySearch !== '', fn ($query) => $query->whereLike('name', '%'.$categorySearch.'%'))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'code']);

            if ($categorySearch !== '') {
                $newProductHasExactCategory = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_category')
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($categorySearch)])
                    ->exists();

                $tokens = collect(preg_split('/\s+/', $categorySearch) ?: [])
                    ->map(fn ($token) => trim($token))
                    ->filter(fn ($token) => mb_strlen($token) >= 3)
                    ->take(3)
                    ->values();

                if ($tokens->isNotEmpty()) {
                    $newProductSimilarCategories = MasterRecord::query()
                        ->forWorkspace($workspaceId)
                        ->ofType('product_category')
                        ->active()
                        ->where(function ($query) use ($tokens) {
                            foreach ($tokens as $token) $query->orWhereLike('name', '%'.$token.'%');
                        })
                        ->when($newProductCategoryMatches->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $newProductCategoryMatches->pluck('id')))
                        ->orderBy('name')
                        ->limit(2)
                        ->get(['id', 'name', 'code']);
                }
            }

                if ($this->newProductCategoryId) {
                    $newProductSelectedCategory = MasterRecord::query()
                        ->forWorkspace($workspaceId)
                        ->ofType('product_category')
                        ->active()
                        ->find($this->newProductCategoryId, ['id', 'name', 'code']);
                }
            }

            $nameSearch = trim($this->newProductName);
            if (mb_strlen($nameSearch) >= 3) {
                $newProductSimilarProducts = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->active()
                    ->with('parent:id,name,status')
                    ->whereLike('name', '%'.$nameSearch.'%')
                    ->when($duplicateProduct, fn ($query) => $query->whereKeyNot($duplicateProduct->id))
                    ->orderBy('name')
                    ->limit(3)
                    ->get(['id', 'type', 'parent_id', 'name', 'code', 'metadata', 'status']);
            }

            if ($this->newProductImage) {
                try {
                    $newProductImagePreview = $this->newProductImage->temporaryUrl();
                } catch (Throwable) {
                    $newProductImagePreview = null;
                }
            }
        }

        return [
            'selectedJob' => null,
            'selectedTask' => null,
            'clients' => $clients,
            'workflows' => $workflows,
            'categories' => collect(),
            'priorities' => $this->createAssignmentReady ? $master->active('priority') : collect(),
            'productionUrgencies' => $this->createAssignmentReady ? $master->active('production_urgency') : collect(),
            'shipmentUrgencies' => $this->createAssignmentReady ? $master->active('shipment_urgency') : collect(),
            'categoryFilterOptions' => $this->createCatalogReady && $canUseOrderProductSelector && $canViewProductCategories
                ? $options->options($user, 'product-categories', 'create-job', '', null, 6)
                : collect(),
            'clientFilterOptions' => $options->options(
                $user,
                'clients',
                'create-job',
                '',
                $this->clientId,
                6,
            ),
            'ownerFilterOptions' => $this->createAssignmentReady
                ? $options->options($user, 'users', 'create-job', '', $this->ownerId, 6)
                : collect(),
            'workflowFilterOptions' => $this->createWorkflowReady
                ? $options->options($user, 'workflows', 'create-job', '', $this->workflowId, 6, ['client_id' => $this->clientId])
                : collect(),
            'productCategories' => $productCategories,
            'productSearchResults' => $productSearchResults,
            'selectedProductDetails' => $selectedProductDetails,
            'activeProductCount' => $activeProductCount,
            'productResultTotal' => $productResultTotal,
            'canUseOrderProductSelector' => $canUseOrderProductSelector,
            'canCreateCatalogProduct' => $canUseOrderProductSelector && $user->canModule('catalog_products', 'create'),
            'canViewProductCategories' => $canViewProductCategories,
            'canCreateProductCategory' => $canViewProductCategories && $user->canModule('product_categories', 'create'),
            'duplicateProduct' => $duplicateProduct,
            'newProductCategoryMatches' => $newProductCategoryMatches,
            'newProductSimilarCategories' => $newProductSimilarCategories,
            'newProductSimilarProducts' => $newProductSimilarProducts,
            'newProductSelectedCategory' => $newProductSelectedCategory,
            'newProductHasExactCategory' => $newProductHasExactCategory,
            'newProductImagePreview' => $newProductImagePreview,
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForCreate($user),
        ];
    }

    private function taskPageData(User $user): array
    {
        app(\App\Services\OrderTaskFlagService::class)->syncDueTransitions();
        $master = app(MasterDataService::class);
        $task = app(TaskService::class)->visibleQuery($user)->with([
            'job.client:id,name,logo_path',
            'job.orderFlag:id,type,name,color,status,sort_order,metadata',
            'job.tasks' => fn ($query) => app(AccessControlService::class)
                ->applyTaskScope($query, $user)
                ->select(['tasks.id', 'tasks.flow_job_id', 'tasks.workflow_phase_id', 'tasks.title'])
                ->orderBy('tasks.id'),
            'assignee', 'phase', 'orderTaskStatus:id,type,name,color,status,sort_order,metadata', 'orderTaskFlag:id,type,name,color,status,sort_order,metadata', 'documentCategory', 'setupTemplate.documentCategory',
            'checklistItems', 'comments.user', 'documents.uploader:id,name', 'activities.user',
        ])->findOrFail($this->selectedTaskId);

        $availableDocuments = $this->showTaskDocumentPicker
            ? app(DocumentService::class)
                ->query($user, ['client' => $task->job?->client_id])
                ->with(['job:id,job_number', 'task:id,title'])
                ->latest('id')
                ->limit(60)
                ->get()
            : collect();

        return [
            'selectedJob' => null,
            'selectedTask' => $task,
            'taskStatuses' => $this->taskStatusOptions($master),
            'priorities' => $master->active('priority'),
            'taskFlags' => $master->active('order_task_flag'),
            'displayTimezone' => app(WorkspaceSettingsService::class)->displayTimezone(),
            'availableDocuments' => $availableDocuments,
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForTask($task, $user),
        ];
    }

    private function jobPageData(User $user): array
    {
        if (! in_array($this->detailTab, ['overview', 'inquiry', 'finance'], true)) {
            $this->detailTab = 'overview';
        }

        $master = app(MasterDataService::class);
        $jobService = app(JobService::class);
        $selected = $jobService->findVisibleBase($user, $this->selectedJobId);
        $jobService->loadVisibleDetailTab($selected, $user, $this->detailTab);

        if ($this->detailTab === 'overview') {
            $jobService->loadVisibleOverviewActivity(
                $selected,
                $this->jobActivityTab,
                $this->jobActivityPage,
                10,
            );
        }

        $availableDocuments = collect();

        $overviewTaskDocumentModalTask = null;
        $overviewTaskAvailableDocuments = collect();
        if ($this->detailTab === 'overview' && $this->showOverviewTaskDocumentModal && $this->overviewTaskDocumentModalTaskId) {
            $overviewTaskDocumentModalTask = $selected->tasks->firstWhere('id', (int) $this->overviewTaskDocumentModalTaskId);
            if ($overviewTaskDocumentModalTask && $this->overviewTaskDocumentSource === 'existing') {
                $overviewTaskAvailableDocuments = app(DocumentService::class)
                    ->query($user, ['client' => $selected->client_id])
                    ->with(['job:id,job_number', 'task:id,title'])
                    ->latest('id')
                    ->limit(60)
                    ->get();
            }
        }

        $inquiryResults = collect();
        $selectedLinkInquiry = null;
        $linkedInquiryCanOpen = false;
        $canViewInquiries = app(AccessControlService::class)->can($user, 'inquiries', 'view');
        $canManageInquiryLink = $this->detailTab === 'inquiry'
            && $canViewInquiries
            && app(AccessControlService::class)->canEditVisibleJob($user, $selected);

        if ($this->detailTab === 'inquiry') {
            if ($selected->sourceInquiry && $canViewInquiries) {
                $linkedInquiryCanOpen = app(InquiryService::class)->visibleQuery($user)
                    ->whereKey($selected->sourceInquiry->id)
                    ->exists();
            }

            if (!$selected->source_inquiry_id && $canManageInquiryLink && mb_strlen(trim($this->inquirySearch)) >= 2) {
                $inquiryResults = $jobService->inquiryLinkResults($user, $selected, $this->inquirySearch, 8);
                if ($this->selectedLinkInquiryId) {
                    $selectedLinkInquiry = $inquiryResults->firstWhere('id', $this->selectedLinkInquiryId);
                    if (!$selectedLinkInquiry) $this->selectedLinkInquiryId = null;
                }
            }
        }

        $financeSummary = null;
        $financeContacts = collect();
        $financeUsers = collect();
        $financeInvoiceTypes = collect();
        $financeCurrencies = collect();
        $financePaymentTerms = collect();
        $financePaymentMethods = collect();
        $financeReceivedAccounts = collect();
        $canCreateFinance = false;
        $canEditFinance = false;
        if ($this->detailTab === 'finance') {
            $financeSummary = app(OrderFinanceService::class)->summary($selected);
            $financeContacts = $selected->client?->contacts ?? collect();
            $financeInvoiceTypes = $master->active('invoice_type');
            $financeCurrencies = $master->active('currency');
            $financePaymentTerms = $master->active('payment_term');
            $financePaymentMethods = $master->active('payment_method');
            $financeReceivedAccounts = $master->active('received_account');
            $canCreateFinance = app(AccessControlService::class)->can($user, 'finance', 'create');
            $canEditFinance = app(AccessControlService::class)->canEditParentRecordModule($user, 'finance', $selected);
            if ($canEditFinance) {
                $financeUsers = User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'profile_image_path']);
            }
        }

        $jobProductSearchResults = collect();
        $jobProductResultTotal = 0;
        $jobProductSelectedProduct = null;
        if ($this->detailTab === 'overview' && $this->showAddJobProductForm) {
            $catalog = app(\App\Services\ProductCatalogService::class);
            $productSearch = trim($this->jobProductSearch);
            $jobProductResultTotal = $catalog->orderSearchCount($productSearch, null);
            $resultLimit = $this->jobProductShowAllResults || $jobProductResultTotal <= 20 ? 20 : 3;
            $jobProductSearchResults = $catalog->searchForOrderCreation($productSearch, null, $resultLimit);
            if ($this->jobProductSelectedId) {
                $jobProductSelectedProduct = $catalog->selectedProducts([$this->jobProductSelectedId])->first();
            }
        }

        return [
            'selectedJob' => $selected,
            'selectedTask' => null,
            'taskStatuses' => $this->detailTab === 'overview' ? $this->taskStatusOptions($master) : collect(),
            'users' => $this->detailTab === 'overview' ? $this->userOptions($user) : collect(),
            'priorities' => $this->detailTab === 'overview' ? $master->active('priority') : collect(),
            // Product/category options on Job Details are loaded remotely only
            // when an inline dropdown opens, avoiding full catalog payloads.
            'products' => collect(),
            'categories' => collect(),
            'availableDocuments' => $availableDocuments,
            'overviewTaskDocumentModalTask' => $overviewTaskDocumentModalTask,
            'overviewTaskAvailableDocuments' => $overviewTaskAvailableDocuments,
            'healthOptions' => collect(),
            'mentionUsers' => $this->detailTab === 'overview'
                ? app(\App\Services\MentionService::class)->optionsForJob($selected, $user)
                : collect(),
            'inquiryResults' => $inquiryResults,
            'selectedLinkInquiry' => $selectedLinkInquiry,
            'canManageInquiryLink' => $canManageInquiryLink,
            'linkedInquiryCanOpen' => $linkedInquiryCanOpen,
            'financeSummary' => $financeSummary,
            'financeContacts' => $financeContacts,
            'financeUsers' => $financeUsers,
            'financeInvoiceTypes' => $financeInvoiceTypes,
            'financeCurrencies' => $financeCurrencies,
            'financePaymentTerms' => $financePaymentTerms,
            'financePaymentMethods' => $financePaymentMethods,
            'financeReceivedAccounts' => $financeReceivedAccounts,
            'canCreateFinance' => $canCreateFinance,
            'canEditFinance' => $canEditFinance,
            'jobProductSearchResults' => $jobProductSearchResults,
            'jobProductResultTotal' => $jobProductResultTotal,
            'jobProductSelectedProduct' => $jobProductSelectedProduct,
        ];
    }

    private function jobsTableData(User $user): array
    {
        // The Orders list is intentionally its own lightweight render branch.
        // It does not hydrate filter catalogs, task collections, members or
        // inline-edit option lists that are not visible in the supplied
        // performance prototype.
        $jobs = app(JobService::class)->paginateOrders(
            $user,
            $this->search,
            $this->perPage,
            $this->client !== '' ? (int) $this->client : null,
            $this->phase !== '' ? (int) $this->phase : null,
            $this->assignee !== '' ? (int) $this->assignee : null,
        );
        $options = app(\App\Services\FilterOptionService::class);

        return [
            'selectedJob' => null,
            'selectedTask' => null,
            'jobs' => $jobs,
            'clientFilterOptions' => $this->client !== ''
                ? $options->options($user, 'clients', 'jobs', '', (int) $this->client, 5)
                : collect(),
            'phaseFilterOptions' => $this->phase !== ''
                ? $options->options($user, 'phases', 'order-list', '', (int) $this->phase, 5)
                : collect(),
            'assigneeFilterOptions' => $this->assignee !== ''
                ? $options->options($user, 'users', 'order-list', '', (int) $this->assignee, 5)
                : collect(),
        ];
    }

    private function userOptions(User $user)
    {
        $isCreator = $this->selectedJobId
            ? FlowJob::query()->whereKey($this->selectedJobId)->where('created_by', $user->id)->exists()
            : false;
        $canAssign = $isCreator || $user->canModule('tasks', 'assign') || $user->canModule('jobs', 'assign');

        return $canAssign
            ? User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'profile_image_path'])
            : collect([(object) ['id' => $user->id, 'name' => $user->name, 'profile_image_path' => $user->profile_image_path]]);
    }

    private function taskStatusOptions(MasterDataService $master)
    {
        return collect(BoardLaneResolver::taskStatuses(
            $master->active('order_task_status')->pluck('name')
        ));
    }

    private function healthOptions()
    {
        return collect(['On Track', 'At Risk', 'Delayed', 'Blocked', 'Completed']);
    }

    private function jobFilters(): array
    {
        return [
            'search' => $this->search,
            'phase' => $this->phase,
            'health' => $this->health,
            'client' => $this->client,
            'owner' => $this->owner,
            'assignee' => $this->assignee,
            'delivery' => $this->delivery,
            'invoice' => $this->invoice,
            'priority' => $this->priorityFilter,
            'status' => $this->jobStatusFilter,
            'sort' => $this->sort,
        ];
    }

    private function resetOverviewTaskResourceUi(): void
    {
        $this->showOverviewTaskDocumentModal = false;
        $this->overviewTaskDocumentModalTaskId = null;
        $this->overviewTaskDocumentSource = 'upload';
        $this->overviewTaskDocumentUpload = null;
        $this->overviewTaskExistingDocumentId = null;
        $this->overviewTaskDocumentNote = '';
        $this->overviewTaskLinkFormTaskId = null;
        $this->overviewTaskLinkUrl = '';
    }

    private function resetJobSelection(): void
    {
        $this->selectedJobIds = [];
        $this->resetPage();
    }
}
