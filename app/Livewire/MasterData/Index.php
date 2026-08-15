<?php

namespace App\Livewire\MasterData;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Models\Client;
use App\Models\MasterRecord;
use App\Services\MasterDataService;
use App\Services\ProductImageService;
use App\Services\ProductCategoryDeletionService;
use App\Support\MasterColor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use UsesPagePlaceholder;
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'group', history: true, except: 'product')]
    public string $group = 'product';

    public string $search = '';
    public string $productMainCategory = '';
    public string $productCategory = '';
    public string $productClientAvailability = '';
    public string $productStatus = '';
    public string $productReferenceCode = '';
    public string $productFormMainCategory = '';
    public string $productSize = '';
    public string $productSubcategory = '';
    public string $productClientAvailabilityMode = 'all';
    public array $productClientIds = [];
    public string $productTestCertificateNumber = '';
    public $productCertificateUpload = null;
    public $productTemplateUpload = null;
    public bool $removeProductCertificate = false;
    public bool $removeProductTemplate = false;
    public int $productPerPage = 10;
    public bool $recordsReady = false;
    public bool $showModal = false;
    public bool $showProductView = false;
    public ?int $viewProductId = null;
    public ?int $editId = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $color = '#2563EB';
    public ?int $parentId = null;
    public string $productCategorySearch = '';
    public string $newProductCategoryName = '';
    public string $newProductCategoryDescription = '';
    public string $newProductCategoryMain = '';
    public string $newMainCategoryName = '';
    public string $newMainCategoryDescription = '';
    public string $newSubcategoryName = '';
    public string $newSubcategoryDescription = '';
    public ?int $newSubcategoryProductCategoryId = null;
    public ?string $categoryCreator = null;
    public string $status = 'active';
    public int $sortOrder = 0;
    public string $metadataJson = '';
    public string $autoInquiryStatus = 'To do';
    public bool $requiresAttention = false;
    public ?int $orderTaskFlagId = null;
    public ?int $orderFlagId = null;
    public $productImage = null;
    public ?string $existingProductImageUrl = null;
    public bool $removeProductImage = false;

    // Product-list bulk selection / actions. Selection is kept by id across pages
    // and can also represent every product matching the current filters.
    public array $selectedProductIds = [];
    public array $excludedProductIds = [];
    public bool $selectAllMatchingProducts = false;
    public ?string $bulkProductPanel = null;
    public string $bulkProductClientMode = 'all';
    public array $bulkProductClientIds = [];
    public string $bulkProductMainCategory = '';
    public ?int $bulkProductCategoryId = null;
    public string $bulkProductSubcategory = '';

    // Product Category hierarchy page state.
    public string $categoryLevelFilter = '';
    public string $categoryParentFilter = '';
    public string $categoryStatusFilter = '';
    public int $categoryPerPage = 6;
    public array $expandedMainCategoryIds = [];
    public array $expandedProductCategoryIds = [];
    public array $categoryProductLimits = [];
    public array $categorySubcategoryLimits = [];

    // Product-category bulk selection. Keys use level:id so hierarchy levels can be mixed safely.
    public array $selectedCategoryKeys = [];

    // Destructive category deletion is always previewed before execution.
    public bool $showCategoryDeleteConfirm = false;
    public array $categoryDeletePreview = [];
    public array $categoryDeleteTargetKeys = [];

    private const CATEGORY_PRODUCT_BATCH = 4;
    private const CATEGORY_SUBCATEGORY_BATCH = 3;
    public ?string $categoryEditorLevel = null;
    public ?int $categoryEditorId = null;
    public ?int $categoryEditorParentId = null;
    public string $categoryEditorName = '';
    public string $categoryEditorDescription = '';
    public string $categoryEditorStatus = 'active';
    public bool $categoryEditorReadOnly = false;

    public function mount(): void
    {
        if (!array_key_exists($this->group, MasterDataService::LABELS)) {
            $this->group = 'product';
        }

        $this->authorizeGroupAction('view');

        // Allow other workflows (for example Create Inquiry) to send the user
        // directly to the standalone Add Product form instead of opening a
        // second inline product-creation modal.
        if ($this->group === 'product' && request()->boolean('create')) {
            $this->open();
        }

        // Sidebar shortcut: open the standalone Product Category creator directly
        // on the Product Categories page. The parent Main Category is selected
        // inside the existing reusable category editor.
        if ($this->group === 'product_category' && request()->boolean('create')) {
            app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();
            $this->openCategoryEditor('product');
        }
    }

    public function selectGroup(string $group): void
    {
        abort_unless(array_key_exists($group, MasterDataService::LABELS), 404);
        $this->authorizeGroupAction('view', $group);
        $this->group = $group;
        $this->recordsReady = true;
        $this->search = '';
        $this->productMainCategory = '';
        $this->productCategory = '';
        $this->productClientAvailability = '';
        $this->productStatus = '';
        $this->categoryLevelFilter = '';
        $this->categoryParentFilter = '';
        $this->categoryStatusFilter = '';
        $this->expandedMainCategoryIds = [];
        $this->expandedProductCategoryIds = [];
        if ($group === 'product_category') {
            // Keep the hierarchy collapsed initially. Child rows are loaded only
            // when their parent is expanded, matching the progressive category
            // loading used by the Product Categories page.
            $this->categoryProductLimits = [];
            $this->categorySubcategoryLimits = [];
        }
        $this->parentId = null;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->clearProductSelection();
        $this->clearCategorySelection();
        $this->closeCategoryDeleteConfirmation();
        $this->resetPage('masterPage');
        $this->resetValidation();
    }

    public function open(?int $id = null): void
    {
        $action = $id ? 'edit' : 'create';
        $this->authorizeGroupAction($action);
        $service = app(MasterDataService::class);
        if ($this->group === 'product') {
            app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();
        }
        $this->recordsReady = true;
        $this->showModal = true;
        $this->editId = $id;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->productCertificateUpload = null;
        $this->productTemplateUpload = null;
        $this->removeProductCertificate = false;
        $this->removeProductTemplate = false;
        $this->resetCategoryCreatorState();
        $this->resetValidation();

        if ($id) {
            $r = MasterRecord::where('workspace_id', $service->workspaceId())->findOrFail($id);
            abort_unless($r->type === $this->group, 404);
            $this->code = $r->code;
            $this->name = $r->name;
            $this->description = (string) $r->description;
            $this->productReferenceCode = $r->productReferenceCode();
            $this->productFormMainCategory = $r->productMainCategory();
            $this->productSize = $r->productSize();
            $this->productSubcategory = trim((string) (data_get($r->metadata, 'sub_category') ?: data_get($r->metadata, 'excel_sub_category')));
            $this->productClientAvailabilityMode = $r->hasSpecificProductAvailability() ? 'specific' : 'all';
            $storedClientIds = collect((array) data_get($r->metadata, 'client_ids', []))->map(fn ($value) => (int) $value)->filter()->values()->all();
            if (!$storedClientIds && $r->hasSpecificProductAvailability()) {
                $labels = collect($r->productAvailabilityLabels())->map(fn ($value) => mb_strtolower(trim((string) $value)))->filter()->all();
                $storedClientIds = Client::query()->where('is_active', true)->get(['id', 'name', 'code'])
                    ->filter(fn (Client $client) => in_array(mb_strtolower((string) $client->name), $labels, true) || in_array(mb_strtolower((string) $client->code), $labels, true))
                    ->pluck('id')->map(fn ($value) => (int) $value)->values()->all();
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
            $this->productFormMainCategory = '';
            $this->productSize = '';
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

    public function close(): void
    {
        $this->showModal = false;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->productCertificateUpload = null;
        $this->productTemplateUpload = null;
        $this->removeProductCertificate = false;
        $this->removeProductTemplate = false;
        $this->resetCategoryCreatorState();
        $this->resetValidation();
    }

    private function resetCategoryCreatorState(): void
    {
        $this->categoryCreator = null;
        $this->newMainCategoryName = '';
        $this->newMainCategoryDescription = '';
        $this->newProductCategoryName = '';
        $this->newProductCategoryDescription = '';
        $this->newProductCategoryMain = '';
        $this->newSubcategoryName = '';
        $this->newSubcategoryDescription = '';
        $this->newSubcategoryProductCategoryId = null;
    }

    public function updatedProductCategorySearch(): void
    {
        if ($this->group !== 'product' || !$this->showModal || $this->editId) return;

        // Typing a new value means the previously selected category is no longer
        // authoritative. A product can only be created once a real category row
        // is selected (or created) from the catalogue picker.
        $this->parentId = null;
        $this->newProductCategoryName = trim($this->productCategorySearch);
        $this->resetValidation(['parentId', 'newProductCategoryName']);
    }

    public function updatedProductFormMainCategory(): void
    {
        if ($this->group === 'product' && $this->showModal) {
            $this->parentId = null;
            $this->productCategorySearch = '';
            $this->productSubcategory = '';
        }
        $this->resetValidation(['productFormMainCategory', 'parentId', 'productSubcategory']);
    }

    /**
     * Server-authoritative handler for the three dependent Product taxonomy
     * selectors. The shared searchable dropdown can optimistically update its
     * label in Alpine, but the dependency chain must always be resolved from
     * canonical taxonomy rows so Product Categories created on the dedicated
     * page appear immediately in Create/Edit Product.
     */
    public function setProductTaxonomySelection(string $property, string $value): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        abort_unless(in_array($property, ['productFormMainCategory', 'parentId', 'productSubcategory'], true), 404);

        $taxonomy = app(\App\Services\ProductTaxonomyService::class);
        $value = trim($value);

        if ($property === 'productFormMainCategory') {
            $main = $taxonomy->mainCategories(true)
                ->first(fn (MasterRecord $record) => mb_strtolower(trim($record->name)) === mb_strtolower($value));

            if (!$main) {
                $this->addError('productFormMainCategory', 'The selected main category is not available.');
                return;
            }

            $this->productFormMainCategory = $main->name;
            $this->parentId = null;
            $this->productCategorySearch = '';
            $this->productSubcategory = '';
            $this->resetValidation(['productFormMainCategory', 'parentId', 'productSubcategory']);
            return;
        }

        if ($property === 'parentId') {
            if ($value === '') {
                $this->parentId = null;
                $this->productCategorySearch = '';
                $this->productSubcategory = '';
                $this->resetValidation(['parentId', 'productSubcategory']);
                return;
            }

            $category = $taxonomy->productCategories(true)->firstWhere('id', (int) $value);
            if (!$category) {
                $this->addError('parentId', 'The selected product category is not available.');
                return;
            }

            $main = $taxonomy->mainCategoryFor($category);
            if (!$main) {
                $this->addError('parentId', 'This product category is not linked to a main category.');
                return;
            }

            if ($this->productFormMainCategory !== ''
                && mb_strtolower(trim($this->productFormMainCategory)) !== mb_strtolower(trim($main->name))) {
                $this->addError('parentId', 'The selected product category does not belong to the selected main category.');
                return;
            }

            $this->productFormMainCategory = $main->name;
            $this->parentId = (int) $category->id;
            $this->productCategorySearch = $category->name;
            $this->productSubcategory = '';
            $this->resetValidation(['productFormMainCategory', 'parentId', 'productSubcategory']);
            return;
        }

        if ($value === '') {
            $this->productSubcategory = '';
            $this->resetValidation('productSubcategory');
            return;
        }

        $categoryId = (int) ($this->parentId ?? 0);
        $subcategory = $taxonomy->subcategories(true)
            ->first(fn (MasterRecord $record) => (int) $record->parent_id === $categoryId
                && mb_strtolower(trim($record->name)) === mb_strtolower($value));

        if (!$subcategory) {
            $this->addError('productSubcategory', 'The selected subcategory is not available under this product category.');
            return;
        }

        $this->productSubcategory = $subcategory->name;
        $this->resetValidation('productSubcategory');
    }

    public function updatedParentId($value): void
    {
        if ($this->group !== 'product' || !$this->showModal) return;

        $this->productSubcategory = '';
        $this->resetValidation(['parentId', 'productSubcategory']);
        $categoryId = (int) $value;
        if ($categoryId <= 0) return;

        $taxonomy = app(\App\Services\ProductTaxonomyService::class);
        $category = $taxonomy->productCategories(true)->firstWhere('id', $categoryId);
        if (!$category) return;

        $this->productCategorySearch = $category->name;
        $categoryMain = $taxonomy->mainCategoryFor($category);
        if ($categoryMain) $this->productFormMainCategory = $categoryMain->name;
    }

    public function updatedProductReferenceCode(): void
    {
        $this->productReferenceCode = trim($this->productReferenceCode);
        $this->resetValidation('productReferenceCode');
    }

    public function updatedProductClientAvailabilityMode(): void
    {
        if ($this->productClientAvailabilityMode === 'all') {
            $this->productClientIds = [];
        }
        $this->resetValidation('productClientIds');
    }

    public function toggleProductClient(int $clientId): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        abort_unless(Client::query()->whereKey($clientId)->where('is_active', true)->exists(), 404);
        $ids = collect($this->productClientIds)->map(fn ($value) => (int) $value);
        $this->productClientIds = $ids->contains($clientId)
            ? $ids->reject(fn ($value) => $value === $clientId)->values()->all()
            : $ids->push($clientId)->unique()->values()->all();
        $this->productClientAvailabilityMode = 'specific';
        $this->resetValidation('productClientIds');
    }

    public function selectAllProductClients(): void
    {
        $this->productClientAvailabilityMode = 'specific';
        $this->productClientIds = Client::query()->where('is_active', true)->orderBy('name')->pluck('id')->map(fn ($value) => (int) $value)->all();
    }

    private function productCodeReadyForCategory(): bool
    {
        return true;
    }

    public function selectProductCategory(int $id): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        if (!$this->productCodeReadyForCategory()) return;

        $category = MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product_category')
            ->active()
            ->findOrFail($id);

        $this->parentId = $category->id;
        $this->productCategorySearch = $category->name;
        $categoryMain = trim((string) (data_get($category->metadata, 'main_category') ?: data_get($category->metadata, 'excel_main_category')));
        if ($categoryMain !== '') $this->productFormMainCategory = $categoryMain;
        $this->productSubcategory = '';
        $this->newProductCategoryName = '';
        $this->resetValidation(['parentId', 'newProductCategoryName', 'productSubcategory']);
        $this->dispatch('product-category-selected');
    }

    public function openCategoryCreator(string $level): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        abort_unless(auth()->user()?->canModule('product_categories', 'create'), 403);
        abort_unless(in_array($level, ['main', 'product', 'sub'], true), 404);

        $this->resetValidation([
            'newMainCategoryName', 'newMainCategoryDescription',
            'newProductCategoryMain', 'newProductCategoryName', 'newProductCategoryDescription',
            'newSubcategoryProductCategoryId', 'newSubcategoryName', 'newSubcategoryDescription',
        ]);
        $this->categoryCreator = $level;

        if ($level === 'product') {
            $this->newProductCategoryMain = trim($this->productFormMainCategory);
        }
        if ($level === 'sub') {
            $this->newSubcategoryProductCategoryId = $this->parentId;
        }
    }

    public function closeCategoryCreator(): void
    {
        $this->categoryCreator = null;
        $this->resetValidation([
            'newMainCategoryName', 'newMainCategoryDescription',
            'newProductCategoryMain', 'newProductCategoryName', 'newProductCategoryDescription',
            'newSubcategoryProductCategoryId', 'newSubcategoryName', 'newSubcategoryDescription',
        ]);
    }

    public function createMainCategory(): void
    {
        $this->assertCanCreateProductTaxonomy('main');
        $this->newMainCategoryName = trim($this->newMainCategoryName);
        $data = $this->validate([
            'newMainCategoryName' => ['required', 'string', 'max:255'],
            'newMainCategoryDescription' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'newMainCategoryName' => 'main category name',
            'newMainCategoryDescription' => 'description',
        ]);

        $name = trim($data['newMainCategoryName']);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $duplicateRecord = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_main_category')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();
        $legacyDuplicate = app(\App\Services\ProductCatalogService::class)->mainCategories()
            ->contains(fn ($value) => mb_strtolower(trim((string) $value)) === mb_strtolower($name));

        if ($duplicateRecord || $legacyDuplicate) {
            $this->addError('newMainCategoryName', 'This main category name already exists. Choose a different name.');
            return;
        }

        $this->createTaxonomyRecord('product_main_category', 'MCAT', $name, $data['newMainCategoryDescription']);
        $this->productFormMainCategory = $name;
        $this->parentId = null;
        $this->productCategorySearch = '';
        $this->productSubcategory = '';
        $this->newMainCategoryName = '';
        $this->newMainCategoryDescription = '';
        $this->categoryCreator = null;
        $this->resetValidation(['productFormMainCategory', 'parentId']);
    }

    public function createProductCategory(): void
    {
        $this->assertCanCreateProductTaxonomy('product');
        $this->newProductCategoryMain = trim($this->newProductCategoryMain);
        $this->newProductCategoryName = trim($this->newProductCategoryName);
        $data = $this->validate([
            'newProductCategoryMain' => ['required', 'string', 'max:255'],
            'newProductCategoryName' => ['required', 'string', 'max:255'],
            'newProductCategoryDescription' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'newProductCategoryMain' => 'main category',
            'newProductCategoryName' => 'product category name',
            'newProductCategoryDescription' => 'description',
        ]);

        $main = trim($data['newProductCategoryMain']);
        $name = trim($data['newProductCategoryName']);
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $existing = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_category')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            $this->addError('newProductCategoryName', $existing->status === 'active'
                ? 'This product category name already exists. Choose a different name.'
                : 'This product category already exists but is inactive. Activate it from Product Categories first.');
            return;
        }

        $mainRecordId = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_main_category')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($main)])
            ->value('id');

        $metadata = ['main_category' => $main, 'excel_main_category' => $main];
        if ($mainRecordId) $metadata['main_category_id'] = (int) $mainRecordId;

        $category = $service->save('product_category', [
            'code' => $service->nextCode('product_category'),
            'name' => $name,
            'description' => $data['newProductCategoryDescription'],
            'status' => 'active',
            'sort_order' => (int) MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->max('sort_order') + 1,
            'metadata' => $metadata,
        ]);

        $this->productFormMainCategory = $main;
        $this->parentId = $category->id;
        $this->productCategorySearch = $category->name;
        $this->productSubcategory = '';
        $this->newProductCategoryName = '';
        $this->newProductCategoryDescription = '';
        $this->newProductCategoryMain = '';
        $this->categoryCreator = null;
        $this->resetValidation(['productFormMainCategory', 'parentId']);
        $this->dispatch('product-category-created');
    }

    public function createProductSubcategory(): void
    {
        $this->assertCanCreateProductTaxonomy('sub');
        $this->newSubcategoryName = trim($this->newSubcategoryName);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $data = $this->validate([
            'newSubcategoryProductCategoryId' => [
                'required', 'integer',
                Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'product_category')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
            'newSubcategoryName' => ['required', 'string', 'max:255'],
            'newSubcategoryDescription' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'newSubcategoryProductCategoryId' => 'product category',
            'newSubcategoryName' => 'subcategory name',
            'newSubcategoryDescription' => 'description',
        ]);

        $categoryId = (int) $data['newSubcategoryProductCategoryId'];
        $name = trim($data['newSubcategoryName']);
        $duplicateRecord = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_subcategory')
            ->where('parent_id', $categoryId)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();
        $legacyDuplicate = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->where('parent_id', $categoryId)
            ->get(['metadata'])
            ->contains(fn (MasterRecord $product) => mb_strtolower(trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category')))) === mb_strtolower($name));

        if ($duplicateRecord || $legacyDuplicate) {
            $this->addError('newSubcategoryName', 'This subcategory name already exists under the selected product category.');
            return;
        }

        $record = $this->createTaxonomyRecord(
            'product_subcategory',
            'SCAT',
            $name,
            $data['newSubcategoryDescription'],
            $categoryId,
        );

        $this->parentId = $categoryId;
        $this->productCategorySearch = (string) $record->parent?->name;
        $categoryMain = trim((string) (data_get($record->parent?->metadata, 'main_category') ?: data_get($record->parent?->metadata, 'excel_main_category')));
        if ($categoryMain !== '') $this->productFormMainCategory = $categoryMain;
        $this->productSubcategory = $record->name;
        $this->newSubcategoryName = '';
        $this->newSubcategoryDescription = '';
        $this->newSubcategoryProductCategoryId = null;
        $this->categoryCreator = null;
        $this->resetValidation(['parentId', 'productSubcategory']);
    }

    private function assertCanCreateProductTaxonomy(string $level): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        abort_unless(in_array($level, ['main', 'product', 'sub'], true), 404);
        abort_unless(auth()->user()?->canModule('product_categories', 'create'), 403);
    }

    private function createTaxonomyRecord(string $type, string $prefix, string $name, ?string $description = null, ?int $parentId = null): MasterRecord
    {
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $highest = MasterRecord::withTrashed()
            ->forWorkspace($workspaceId)
            ->ofType($type)
            ->where('code', 'like', $prefix.'-%')
            ->pluck('code')
            ->reduce(function (int $max, string $code) use ($prefix): int {
                return preg_match('/^'.preg_quote($prefix, '/').'-(\\d+)$/', $code, $matches)
                    ? max($max, (int) $matches[1])
                    : $max;
            }, 0);

        $record = new MasterRecord();
        $record->fill([
            'workspace_id' => $workspaceId,
            'parent_id' => $parentId,
            'type' => $type,
            'code' => $prefix.'-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT),
            'name' => trim($name),
            'description' => blank($description) ? null : trim((string) $description),
            'metadata' => null,
            'status' => 'active',
            'sort_order' => (int) MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->max('sort_order') + 1,
        ]);
        if (Schema::hasColumn('master_records', 'created_by')) $record->created_by = auth()->id();
        $record->save();

        return $record->load('parent');
    }

    public function updatedSearch(): void
    {
        $this->recordsReady = true;
        $this->clearProductSelection();
        if ($this->group === 'product_category') {
            $this->clearCategorySelection();
            $this->resetCategoryLazyLoading();
        }
        $this->resetPage('masterPage');
    }

    public function updatedProductMainCategory(): void
    {
        $this->recordsReady = true;
        $this->clearProductSelection();
        $this->resetPage('masterPage');
    }

    public function updatedProductCategory(): void
    {
        $this->recordsReady = true;
        $this->clearProductSelection();
        $this->resetPage('masterPage');
    }

    public function updatedProductClientAvailability(): void
    {
        $this->recordsReady = true;
        $this->clearProductSelection();
        $this->resetPage('masterPage');
    }

    public function updatedProductStatus(): void
    {
        $this->recordsReady = true;
        $this->clearProductSelection();
        $this->resetPage('masterPage');
    }

    public function updatedProductPerPage($value): void
    {
        $value = (int) $value;
        $this->productPerPage = in_array($value, [10, 20, 50, 100], true) ? $value : 10;
        $this->recordsReady = true;
        $this->resetPage('masterPage');
    }

    public function clearProductFilters(): void
    {
        $this->search = '';
        $this->productMainCategory = '';
        $this->productCategory = '';
        $this->productClientAvailability = '';
        $this->productStatus = '';
        $this->recordsReady = true;
        $this->clearProductSelection();
        $this->resetPage('masterPage');
    }

    public function loadMasterRecords(): void
    {
        // Product Categories intentionally do not synchronize or preload their
        // complete hierarchy here. Main categories render first; descendants are
        // requested only after expansion. Taxonomy synchronization still runs on
        // create/edit operations where it is actually required.
        $this->recordsReady = true;
    }

    public function updatedCategoryLevelFilter(): void
    {
        $this->recordsReady = true;
        $this->clearCategorySelection();
        $this->resetCategoryLazyLoading();
        $this->resetPage('masterPage');
    }

    public function updatedCategoryParentFilter(): void
    {
        $this->recordsReady = true;
        $this->clearCategorySelection();
        $this->resetCategoryLazyLoading();
        $this->resetPage('masterPage');
    }

    public function updatedCategoryStatusFilter(): void
    {
        $this->recordsReady = true;
        $this->clearCategorySelection();
        $this->resetCategoryLazyLoading();
        $this->resetPage('masterPage');
    }

    public function updatedCategoryPerPage($value): void
    {
        $value = (int) $value;
        $this->categoryPerPage = in_array($value, [6, 10, 20, 50], true) ? $value : 6;
        $this->recordsReady = true;
        $this->resetCategoryLazyLoading();
        $this->resetPage('masterPage');
    }

    public function clearCategoryFilters(): void
    {
        $this->search = '';
        $this->categoryLevelFilter = '';
        $this->categoryParentFilter = '';
        $this->categoryStatusFilter = '';
        $this->recordsReady = true;
        $this->clearCategorySelection();
        $this->resetCategoryLazyLoading();
        $this->resetPage('masterPage');
    }

    private function resetCategoryLazyLoading(bool $collapse = true): void
    {
        $this->categoryProductLimits = [];
        $this->categorySubcategoryLimits = [];
        if ($collapse) {
            $this->expandedMainCategoryIds = [];
            $this->expandedProductCategoryIds = [];
        }
    }

    public function toggleCategoryExpansion(string $level, int $id): void
    {
        abort_unless($this->group === 'product_category', 404);
        $property = $level === 'main' ? 'expandedMainCategoryIds' : ($level === 'product' ? 'expandedProductCategoryIds' : null);
        abort_unless($property, 404);

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $expectedType = $level === 'main' ? 'product_main_category' : 'product_category';
        MasterRecord::query()->forWorkspace($workspaceId)->ofType($expectedType)->findOrFail($id);

        $values = collect($this->{$property})->map(fn ($value) => (int) $value);
        $opening = ! $values->contains($id);
        $this->{$property} = $opening
            ? $values->push($id)->unique()->values()->all()
            : $values->reject(fn ($value) => $value === $id)->values()->all();

        if ($opening && $level === 'main') {
            $this->categoryProductLimits[$id] = max(self::CATEGORY_PRODUCT_BATCH, (int) ($this->categoryProductLimits[$id] ?? 0));
        }
        if ($opening && $level === 'product') {
            $this->categorySubcategoryLimits[$id] = max(self::CATEGORY_SUBCATEGORY_BATCH, (int) ($this->categorySubcategoryLimits[$id] ?? 0));
        }
    }

    public function loadMoreCategoryProducts(int $mainCategoryId): void
    {
        abort_unless($this->group === 'product_category', 404);
        MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product_main_category')
            ->findOrFail($mainCategoryId);

        $this->categoryProductLimits[$mainCategoryId] = max(
            self::CATEGORY_PRODUCT_BATCH,
            (int) ($this->categoryProductLimits[$mainCategoryId] ?? self::CATEGORY_PRODUCT_BATCH) + self::CATEGORY_PRODUCT_BATCH
        );
    }

    public function loadMoreCategorySubcategories(int $productCategoryId): void
    {
        abort_unless($this->group === 'product_category', 404);
        MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product_category')
            ->findOrFail($productCategoryId);

        $this->categorySubcategoryLimits[$productCategoryId] = max(
            self::CATEGORY_SUBCATEGORY_BATCH,
            (int) ($this->categorySubcategoryLimits[$productCategoryId] ?? self::CATEGORY_SUBCATEGORY_BATCH) + self::CATEGORY_SUBCATEGORY_BATCH
        );
    }

    public function expandAllCategories(): void
    {
        abort_unless($this->group === 'product_category', 404);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $this->expandedMainCategoryIds = MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_main_category')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->expandedProductCategoryIds = MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->pluck('id')->map(fn ($id) => (int) $id)->all();
        foreach ($this->expandedMainCategoryIds as $id) {
            $this->categoryProductLimits[(int) $id] = self::CATEGORY_PRODUCT_BATCH;
        }
        foreach ($this->expandedProductCategoryIds as $id) {
            $this->categorySubcategoryLimits[(int) $id] = self::CATEGORY_SUBCATEGORY_BATCH;
        }
    }

    public function collapseAllCategories(): void
    {
        abort_unless($this->group === 'product_category', 404);
        $this->resetCategoryLazyLoading();
    }


    private function categoryTypeForLevel(string $level): string
    {
        return match ($level) {
            'main' => 'product_main_category',
            'product' => 'product_category',
            'sub' => 'product_subcategory',
            default => abort(404),
        };
    }

    private function normalizeCategorySelectionKey(string $key): ?array
    {
        if (! preg_match('/^(main|product|sub):(\d+)$/', trim($key), $matches)) return null;
        return [$matches[1], (int) $matches[2]];
    }

    public function toggleCategorySelection(string $level, int $id): void
    {
        abort_unless($this->group === 'product_category', 404);
        $type = $this->categoryTypeForLevel($level);
        MasterRecord::query()->forWorkspace(app(MasterDataService::class)->workspaceId())->ofType($type)->findOrFail($id);

        $key = $level.':'.$id;
        $selected = collect($this->selectedCategoryKeys)->map(fn ($value) => (string) $value);
        $this->selectedCategoryKeys = $selected->contains($key)
            ? $selected->reject(fn ($value) => $value === $key)->values()->all()
            : $selected->push($key)->unique()->values()->all();
    }

    public function toggleCategoryPageSelection(array $keys, bool $checked): void
    {
        abort_unless($this->group === 'product_category', 404);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $parsedKeys = collect($keys)
            ->map(fn ($key) => $this->normalizeCategorySelectionKey((string) $key))
            ->filter()
            ->groupBy(fn ($pair) => $pair[0]);
        $validKeys = collect();
        foreach (['main', 'product', 'sub'] as $level) {
            $ids = collect($parsedKeys->get($level, collect()))->pluck(1)->map(fn ($id) => (int) $id)->unique()->values();
            if ($ids->isEmpty()) continue;
            MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType($this->categoryTypeForLevel($level))
                ->whereIn('id', $ids->all())
                ->pluck('id')
                ->each(fn ($id) => $validKeys->push($level.':'.(int) $id));
        }
        $validKeys = $validKeys->unique()->values();

        if ($validKeys->isEmpty()) return;
        $selected = collect($this->selectedCategoryKeys)->map(fn ($value) => (string) $value);
        $this->selectedCategoryKeys = $checked
            ? $selected->concat($validKeys)->unique()->values()->all()
            : $selected->reject(fn ($value) => $validKeys->contains($value))->values()->all();
    }

    public function clearCategorySelection(): void
    {
        $this->selectedCategoryKeys = [];
    }

    private function selectedCategoryRecords(): \Illuminate\Support\Collection
    {
        if ($this->group !== 'product_category') return collect();
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $grouped = collect($this->selectedCategoryKeys)
            ->map(fn ($key) => $this->normalizeCategorySelectionKey((string) $key))
            ->filter()
            ->groupBy(fn ($pair) => $pair[0]);

        return collect(['main', 'product', 'sub'])->flatMap(function (string $level) use ($grouped, $workspaceId) {
            $ids = collect($grouped->get($level, collect()))->pluck(1)->map(fn ($id) => (int) $id)->unique()->values();
            if ($ids->isEmpty()) return collect();
            return MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType($this->categoryTypeForLevel($level))
                ->whereIn('id', $ids->all())
                ->get()
                ->map(fn (MasterRecord $record) => ['level' => $level, 'record' => $record]);
        })->values();
    }

    public function bulkSetCategoryStatus(string $status): void
    {
        abort_unless($this->group === 'product_category', 404);
        $this->authorizeGroupAction('edit');
        abort_unless(in_array($status, ['active', 'inactive'], true), 422);
        $selected = $this->selectedCategoryRecords();
        if ($selected->isEmpty()) return;

        foreach ($selected as $item) {
            $record = $item['record'];
            if ($record->status === $status) continue;
            $record->status = $status;
            $record->save();
        }

        $count = $selected->count();
        $this->clearCategorySelection();
        $this->recordsReady = true;
        session()->flash('success', number_format($count).' '.strtolower(\Illuminate\Support\Str::plural('category', $count)).' set to '.$status.'.');
    }

    public function exportSelectedCategories()
    {
        abort_unless($this->group === 'product_category', 404);
        $this->authorizeGroupAction('view');
        $selected = $this->selectedCategoryRecords();
        if ($selected->isEmpty()) return null;
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $filename = 'flowtrack-product-categories-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($selected, $workspaceId): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Category', 'Code', 'Level', 'Parent', 'Status', 'Updated']);
            foreach ($selected as $item) {
                $record = $item['record'];
                $level = $item['level'];
                $parent = '—';
                if ($level === 'product') {
                    $mainId = (int) data_get($record->metadata, 'main_category_id', 0);
                    $parent = $mainId > 0
                        ? (string) (MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_main_category')->whereKey($mainId)->value('name') ?: '—')
                        : (string) (data_get($record->metadata, 'main_category') ?: data_get($record->metadata, 'excel_main_category') ?: '—');
                } elseif ($level === 'sub') {
                    $parent = (string) (MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->whereKey($record->parent_id)->value('name') ?: '—');
                }
                fputcsv($out, [
                    $record->name,
                    $record->code,
                    match ($level) { 'main' => 'Main category', 'product' => 'Product category', default => 'Subcategory' },
                    $parent,
                    ucfirst((string) $record->status),
                    optional($record->updated_at)->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function openCategoryDeleteConfirmation(?string $level = null, ?int $id = null): void
    {
        abort_unless($this->group === 'product_category', 404);
        $this->authorizeGroupAction('delete');

        $workspaceId = app(MasterDataService::class)->workspaceId();
        app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();

        if ($level !== null && $id !== null) {
            $type = $this->categoryTypeForLevel($level);
            MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->findOrFail($id);
            $keys = [$level.':'.$id];
        } else {
            $keys = collect($this->selectedCategoryKeys)->map(fn ($key) => (string) $key)->unique()->values()->all();
        }

        if ($keys === []) return;

        $preview = app(ProductCategoryDeletionService::class)->preview($workspaceId, $keys);
        if (($preview['total_categories'] ?? 0) < 1) {
            $this->addError('record', 'The selected categories no longer exist. Refresh the page and try again.');
            return;
        }

        $this->categoryDeleteTargetKeys = $keys;
        $this->categoryDeletePreview = $preview;
        $this->showCategoryDeleteConfirm = true;
    }

    public function closeCategoryDeleteConfirmation(): void
    {
        $this->showCategoryDeleteConfirm = false;
        $this->categoryDeletePreview = [];
        $this->categoryDeleteTargetKeys = [];
    }

    /**
     * Backward-compatible bulk entry point. Deletion never happens without the impact modal.
     */
    public function bulkDeleteCategories(): void
    {
        $this->openCategoryDeleteConfirmation();
    }

    public function confirmCategoryHardDelete(): void
    {
        abort_unless($this->group === 'product_category', 404);
        $this->authorizeGroupAction('delete');
        if ($this->categoryDeleteTargetKeys === []) return;

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $result = app(ProductCategoryDeletionService::class)->hardDelete($workspaceId, $this->categoryDeleteTargetKeys);

        $deletedCategories = (int) ($result['total_categories'] ?? 0);
        $unassignedProducts = (int) ($result['products'] ?? 0);

        $this->closeCategoryDeleteConfirmation();
        $this->clearCategorySelection();
        $this->resetCategoryLazyLoading();
        $this->recordsReady = true;
        $this->resetPage('masterPage');

        session()->flash(
            'success',
            number_format($deletedCategories).' '.strtolower(\Illuminate\Support\Str::plural('category', $deletedCategories)).' permanently deleted'
            .($unassignedProducts > 0 ? ' and '.number_format($unassignedProducts).' '.strtolower(\Illuminate\Support\Str::plural('product', $unassignedProducts)).' unassigned.' : '.')
        );
    }

    public function openCategoryEditor(string $level = 'main', ?int $id = null, ?int $parentId = null, bool $readOnly = false): void
    {
        abort_unless($this->group === 'product_category', 404);
        abort_unless(in_array($level, ['main', 'product', 'sub'], true), 404);
        $this->authorizeGroupAction($readOnly ? 'view' : ($id ? 'edit' : 'create'));
        app(\App\Services\ProductTaxonomyService::class)->synchronizeLegacyTaxonomy();

        $this->categoryEditorReadOnly = $readOnly;
        $this->categoryEditorLevel = $level;
        $this->categoryEditorId = $id;
        $this->categoryEditorParentId = $parentId;
        $this->categoryEditorName = '';
        $this->categoryEditorDescription = '';
        $this->categoryEditorStatus = 'active';
        $this->resetValidation([
            'categoryEditorLevel', 'categoryEditorParentId', 'categoryEditorName',
            'categoryEditorDescription', 'categoryEditorStatus',
        ]);

        if (!$id) return;

        $type = match ($level) {
            'main' => 'product_main_category',
            'product' => 'product_category',
            'sub' => 'product_subcategory',
        };
        $record = MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType($type)
            ->findOrFail($id);

        $this->categoryEditorName = $record->name;
        $this->categoryEditorDescription = (string) $record->description;
        $this->categoryEditorStatus = $record->status;
        if ($level === 'product') {
            $this->categoryEditorParentId = app(\App\Services\ProductTaxonomyService::class)->mainCategoryFor($record)?->id;
        } elseif ($level === 'sub') {
            $this->categoryEditorParentId = $record->parent_id;
        }
    }

    public function viewCategory(string $level, int $id): void
    {
        $this->openCategoryEditor($level, $id, null, true);
    }

    public function updatedCategoryEditorLevel(): void
    {
        if ($this->categoryEditorId) return;
        $this->categoryEditorParentId = null;
        $this->resetValidation(['categoryEditorParentId', 'categoryEditorName']);
    }

    public function closeCategoryEditor(): void
    {
        $this->categoryEditorLevel = null;
        $this->categoryEditorId = null;
        $this->categoryEditorParentId = null;
        $this->categoryEditorName = '';
        $this->categoryEditorDescription = '';
        $this->categoryEditorStatus = 'active';
        $this->categoryEditorReadOnly = false;
        $this->resetValidation([
            'categoryEditorLevel', 'categoryEditorParentId', 'categoryEditorName',
            'categoryEditorDescription', 'categoryEditorStatus',
        ]);
    }

    public function saveCategoryEditor(): void
    {
        abort_unless($this->group === 'product_category', 404);
        abort_unless(!$this->categoryEditorReadOnly, 403);
        abort_unless(in_array($this->categoryEditorLevel, ['main', 'product', 'sub'], true), 404);
        $this->authorizeGroupAction($this->categoryEditorId ? 'edit' : 'create');

        $workspaceId = app(MasterDataService::class)->workspaceId();
        $level = (string) $this->categoryEditorLevel;
        $type = match ($level) {
            'main' => 'product_main_category',
            'product' => 'product_category',
            'sub' => 'product_subcategory',
        };

        $rules = [
            'categoryEditorName' => ['required', 'string', 'max:255'],
            'categoryEditorDescription' => ['nullable', 'string', 'max:5000'],
            'categoryEditorStatus' => ['required', Rule::in(['active', 'inactive'])],
        ];
        if ($level !== 'main') {
            $parentType = $level === 'product' ? 'product_main_category' : 'product_category';
            $rules['categoryEditorParentId'] = [
                'required', 'integer',
                Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', $parentType)
                    ->whereNull('deleted_at')),
            ];
        }
        $data = $this->validate($rules, [], [
            'categoryEditorName' => 'name',
            'categoryEditorDescription' => 'description',
            'categoryEditorStatus' => 'status',
            'categoryEditorParentId' => $level === 'product' ? 'main category' : 'product category',
        ]);

        $name = trim($data['categoryEditorName']);
        $duplicate = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType($type)
            ->when($level === 'sub', fn ($q) => $q->where('parent_id', (int) $data['categoryEditorParentId']))
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($this->categoryEditorId, fn ($q) => $q->where('id', '!=', $this->categoryEditorId))
            ->exists();
        if ($duplicate) {
            $this->addError('categoryEditorName', 'This category name already exists at this level.');
            return;
        }

        $record = $this->categoryEditorId
            ? MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->findOrFail($this->categoryEditorId)
            : new MasterRecord();
        $oldName = (string) $record->name;
        $oldParentId = (int) ($record->parent_id ?? 0);

        if (!$record->exists) {
            $prefix = match ($level) { 'main' => 'MCAT', 'product' => 'CAT', 'sub' => 'SCAT' };
            $highest = MasterRecord::withTrashed()->forWorkspace($workspaceId)->ofType($type)
                ->where('code', 'like', $prefix.'-%')->pluck('code')
                ->reduce(function (int $max, string $code) use ($prefix): int {
                    return preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/', $code, $matches) ? max($max, (int) $matches[1]) : $max;
                }, 0);
            $record->workspace_id = $workspaceId;
            $record->type = $type;
            $record->code = $prefix.'-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);
            $record->sort_order = ((int) MasterRecord::query()->forWorkspace($workspaceId)->ofType($type)->max('sort_order')) + 1;
            if (Schema::hasColumn('master_records', 'created_by')) $record->created_by = auth()->id();
        }

        $record->name = $name;
        $record->description = blank($data['categoryEditorDescription']) ? null : trim((string) $data['categoryEditorDescription']);
        $record->status = $data['categoryEditorStatus'];

        if ($level === 'main') {
            $record->parent_id = null;
            $record->metadata = $record->metadata ?: null;
        } elseif ($level === 'product') {
            $main = MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_main_category')->findOrFail((int) $data['categoryEditorParentId']);
            $metadata = (array) ($record->metadata ?? []);
            $metadata['main_category'] = $main->name;
            $metadata['excel_main_category'] = $main->name;
            $metadata['main_category_id'] = $main->id;
            $record->parent_id = null;
            $record->metadata = $metadata;
        } else {
            $record->parent_id = (int) $data['categoryEditorParentId'];
        }
        $record->save();

        if ($level === 'main' && $oldName !== '' && mb_strtolower($oldName) !== mb_strtolower($record->name)) {
            MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->get()->each(function (MasterRecord $category) use ($oldName, $record): void {
                $metadata = (array) ($category->metadata ?? []);
                $mainId = (int) ($metadata['main_category_id'] ?? 0);
                $mainName = trim((string) ($metadata['main_category'] ?? $metadata['excel_main_category'] ?? ''));
                if ($mainId !== (int) $record->id && mb_strtolower($mainName) !== mb_strtolower($oldName)) return;
                $metadata['main_category'] = $record->name;
                $metadata['excel_main_category'] = $record->name;
                $metadata['main_category_id'] = $record->id;
                $category->metadata = $metadata;
                $category->saveQuietly();
                MasterRecord::query()->forWorkspace($category->workspace_id)->ofType('product')->where('parent_id', $category->id)->get()->each(function (MasterRecord $product) use ($record): void {
                    $productMetadata = (array) ($product->metadata ?? []);
                    $productMetadata['main_category'] = $record->name;
                    $productMetadata['excel_main_category'] = $record->name;
                    $product->metadata = $productMetadata;
                    $product->saveQuietly();
                });
            });
        }

        if ($level === 'product') {
            $main = MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_main_category')->find((int) $this->categoryEditorParentId);
            if ($main) {
                MasterRecord::query()->forWorkspace($workspaceId)->ofType('product')->where('parent_id', $record->id)->get()->each(function (MasterRecord $product) use ($main): void {
                    $metadata = (array) ($product->metadata ?? []);
                    $metadata['main_category'] = $main->name;
                    $metadata['excel_main_category'] = $main->name;
                    $product->metadata = $metadata;
                    $product->saveQuietly();
                });
            }
        }

        if ($level === 'sub' && $this->categoryEditorId && $oldName !== '' && (mb_strtolower($oldName) !== mb_strtolower($record->name) || $oldParentId !== (int) $record->parent_id)) {
            MasterRecord::query()->forWorkspace($workspaceId)->ofType('product')->where('parent_id', $oldParentId)->get()->each(function (MasterRecord $product) use ($oldName, $record): void {
                $metadata = (array) ($product->metadata ?? []);
                $current = trim((string) ($metadata['sub_category'] ?? $metadata['excel_sub_category'] ?? ''));
                if (mb_strtolower($current) !== mb_strtolower($oldName)) return;
                $metadata['sub_category'] = $record->name;
                $metadata['excel_sub_category'] = $record->name;
                $product->parent_id = $record->parent_id;
                $product->metadata = $metadata;
                $product->saveQuietly();
            });
        }

        if ($level === 'product' && $this->categoryEditorParentId) {
            $this->expandedMainCategoryIds = collect($this->expandedMainCategoryIds)->push((int) $this->categoryEditorParentId)->unique()->values()->all();
        } elseif ($level === 'sub' && $record->parent_id) {
            $this->expandedProductCategoryIds = collect($this->expandedProductCategoryIds)->push((int) $record->parent_id)->unique()->values()->all();
        }

        $wasEditing = (bool) $this->categoryEditorId;
        $this->recordsReady = true;
        $this->closeCategoryEditor();
        session()->flash('success', $wasEditing ? 'Category updated.' : 'Category created.');
    }

    public function toggleCategoryStatus(string $level, int $id): void
    {
        abort_unless($this->group === 'product_category', 404);
        $this->authorizeGroupAction('edit');
        $type = match ($level) { 'main' => 'product_main_category', 'product' => 'product_category', 'sub' => 'product_subcategory', default => abort(404) };
        $record = MasterRecord::query()->forWorkspace(app(MasterDataService::class)->workspaceId())->ofType($type)->findOrFail($id);
        $record->status = $record->status === 'active' ? 'inactive' : 'active';
        $record->save();
        $this->recordsReady = true;
        session()->flash('success', 'Category status updated.');
    }

    public function deleteCategory(string $level, int $id): void
    {
        $this->openCategoryDeleteConfirmation($level, $id);
    }

    public function updatedProductImage(): void
    {
        abort_unless($this->group === 'product', 404);
        $this->removeProductImage = false;
        $this->validateOnly('productImage', [
            'productImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    public function markProductImageForRemoval(): void
    {
        abort_unless($this->group === 'product', 404);
        $this->removeProductImage = true;
        $this->productImage = null;
        $this->resetValidation('productImage');
    }

    public function restoreProductImage(): void
    {
        $this->removeProductImage = false;
    }

    public function updatedProductCertificateUpload(): void
    {
        $this->removeProductCertificate = false;
        $this->validateOnly('productCertificateUpload', ['productCertificateUpload' => ['nullable', 'file', 'max:10240']]);
    }

    public function updatedProductTemplateUpload(): void
    {
        $this->removeProductTemplate = false;
        $this->validateOnly('productTemplateUpload', ['productTemplateUpload' => ['nullable', 'file', 'max:10240']]);
    }

    public function clearProductCertificateUpload(): void
    {
        $this->productCertificateUpload = null;
        $this->resetValidation('productCertificateUpload');
    }

    public function clearProductTemplateUpload(): void
    {
        $this->productTemplateUpload = null;
        $this->resetValidation('productTemplateUpload');
    }

    public function removeProductCertificate(): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        $this->productCertificateUpload = null;
        $this->removeProductCertificate = true;
        $this->resetValidation('productCertificateUpload');
    }

    public function removeProductTemplate(): void
    {
        abort_unless($this->group === 'product' && $this->showModal, 404);
        $this->productTemplateUpload = null;
        $this->removeProductTemplate = true;
        $this->resetValidation('productTemplateUpload');
    }

    public function saveProductDraft(): void
    {
        $this->status = 'inactive';
        $this->save();
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
            'name' => ['required', 'string', 'max:255'],
            'productReferenceCode' => $this->group === 'product' ? ['nullable', 'string', 'max:80'] : ['nullable'],
            'productFormMainCategory' => $this->group === 'product' ? ['required', 'string', 'max:255'] : ['nullable'],
            'productSize' => $this->group === 'product' ? ['nullable', 'string', 'max:1200'] : ['nullable'],
            'productSubcategory' => $this->group === 'product' ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'productClientAvailabilityMode' => $this->group === 'product' ? ['required', Rule::in(['all', 'specific'])] : ['nullable'],
            'productClientIds' => $this->group === 'product' && $this->productClientAvailabilityMode === 'specific' ? ['required', 'array', 'min:1'] : ['array'],
            'productClientIds.*' => ['integer', Rule::exists('clients', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'productTestCertificateNumber' => $this->group === 'product' ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'productCertificateUpload' => $this->group === 'product' ? ['nullable', 'file', 'max:10240'] : ['nullable'],
            'productTemplateUpload' => $this->group === 'product' ? ['nullable', 'file', 'max:10240'] : ['nullable'],
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
        }

        $metadata = null;
        if (filled($data['metadataJson'])) {
            $metadata = json_decode($data['metadataJson'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($metadata)) {
                throw ValidationException::withMessages(['metadataJson' => 'Metadata must be valid JSON.']);
            }
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
            $metadata['main_category'] = trim((string) $data['productFormMainCategory']);
            $metadata['product_size'] = trim((string) $data['productSize']) ?: null;
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
        $record = $service->save($this->group, [
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

    private function productFilterValues(): array
    {
        return [
            'main_category' => $this->productMainCategory,
            'parent_id' => $this->productCategory,
            'client_availability' => $this->productClientAvailability,
            'status' => $this->productStatus,
        ];
    }

    private function filteredProductsQuery()
    {
        return app(MasterDataService::class)->query('product', $this->search, $this->productFilterValues());
    }

    private function selectedProductsQuery()
    {
        $workspaceId = app(MasterDataService::class)->workspaceId();

        if ($this->selectAllMatchingProducts) {
            return $this->filteredProductsQuery()
                ->when($this->excludedProductIds, fn ($query) => $query->whereNotIn('master_records.id', $this->excludedProductIds));
        }

        return MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product')
            ->with(['parent', 'creator'])
            ->whereIn('id', collect($this->selectedProductIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all());
    }

    public function toggleProductSelection(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        MasterRecord::query()->forWorkspace(app(MasterDataService::class)->workspaceId())->ofType('product')->findOrFail($id);

        if ($this->selectAllMatchingProducts) {
            $excluded = collect($this->excludedProductIds)->map(fn ($value) => (int) $value);
            $this->excludedProductIds = $excluded->contains($id)
                ? $excluded->reject(fn ($value) => $value === $id)->values()->all()
                : $excluded->push($id)->unique()->values()->all();
            return;
        }

        $selected = collect($this->selectedProductIds)->map(fn ($value) => (int) $value);
        $this->selectedProductIds = $selected->contains($id)
            ? $selected->reject(fn ($value) => $value === $id)->values()->all()
            : $selected->push($id)->unique()->values()->all();
    }

    public function toggleProductPageSelection(array $ids, bool $checked): void
    {
        abort_unless($this->group === 'product', 404);
        $ids = collect($ids)->map(fn ($value) => (int) $value)->filter()->unique()->values();
        if ($ids->isEmpty()) return;

        $validIds = MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product')
            ->whereIn('id', $ids->all())
            ->pluck('id')->map(fn ($value) => (int) $value);

        if ($this->selectAllMatchingProducts) {
            $excluded = collect($this->excludedProductIds)->map(fn ($value) => (int) $value);
            $this->excludedProductIds = $checked
                ? $excluded->reject(fn ($value) => $validIds->contains($value))->values()->all()
                : $excluded->concat($validIds)->unique()->values()->all();
            return;
        }

        $selected = collect($this->selectedProductIds)->map(fn ($value) => (int) $value);
        $this->selectedProductIds = $checked
            ? $selected->concat($validIds)->unique()->values()->all()
            : $selected->reject(fn ($value) => $validIds->contains($value))->values()->all();
    }

    public function selectAllFilteredProducts(): void
    {
        abort_unless($this->group === 'product', 404);
        $this->selectAllMatchingProducts = true;
        $this->selectedProductIds = [];
        $this->excludedProductIds = [];
    }

    public function clearProductSelection(): void
    {
        $this->selectedProductIds = [];
        $this->excludedProductIds = [];
        $this->selectAllMatchingProducts = false;
        $this->bulkProductPanel = null;
        $this->resetBulkProductActionState();
    }

    private function resetBulkProductActionState(): void
    {
        $this->bulkProductClientMode = 'all';
        $this->bulkProductClientIds = [];
        $this->bulkProductMainCategory = '';
        $this->bulkProductCategoryId = null;
        $this->bulkProductSubcategory = '';
        $this->resetValidation([
            'bulkProductClientMode', 'bulkProductClientIds', 'bulkProductMainCategory',
            'bulkProductCategoryId', 'bulkProductSubcategory',
        ]);
    }

    public function openProductBulkPanel(string $panel): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(in_array($panel, ['clients', 'category'], true), 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        abort_if($this->productSelectionCount() < 1, 422, 'Select at least one product.');

        $this->resetBulkProductActionState();
        $this->bulkProductPanel = $panel;
    }

    public function closeProductBulkPanel(): void
    {
        $this->bulkProductPanel = null;
        $this->resetBulkProductActionState();
    }

    public function updatedBulkProductMainCategory(): void
    {
        $this->bulkProductCategoryId = null;
        $this->bulkProductSubcategory = '';
        $this->resetValidation(['bulkProductCategoryId', 'bulkProductSubcategory']);
    }

    public function updatedBulkProductCategoryId(): void
    {
        $this->bulkProductSubcategory = '';
        $this->resetValidation(['bulkProductCategoryId', 'bulkProductSubcategory']);
    }

    public function toggleBulkProductClient(int $clientId): void
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        abort_unless(Client::query()->whereKey($clientId)->where('is_active', true)->exists(), 404);
        $ids = collect($this->bulkProductClientIds)->map(fn ($value) => (int) $value);
        $this->bulkProductClientIds = $ids->contains($clientId)
            ? $ids->reject(fn ($value) => $value === $clientId)->values()->all()
            : $ids->push($clientId)->unique()->values()->all();
        $this->bulkProductClientMode = 'specific';
        $this->resetValidation('bulkProductClientIds');
    }

    public function bulkSetProductStatus(string $status): void
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        abort_unless(in_array($status, ['active', 'inactive'], true), 422);
        $count = $this->productSelectionCount();
        if ($count < 1) return;

        $this->selectedProductsQuery()->reorder()->update(['status' => $status]);
        $this->clearProductSelection();
        $this->recordsReady = true;
        session()->flash('success', number_format($count).' '.strtolower(\Illuminate\Support\Str::plural('product', $count)).' set to '.$status.'.');
    }

    public function applyBulkProductClients(): void
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        $this->validate([
            'bulkProductClientMode' => ['required', Rule::in(['all', 'specific'])],
            'bulkProductClientIds' => $this->bulkProductClientMode === 'specific' ? ['required', 'array', 'min:1'] : ['array'],
            'bulkProductClientIds.*' => ['integer', Rule::exists('clients', 'id')->where(fn ($q) => $q->where('is_active', true))],
        ]);
        $count = $this->productSelectionCount();
        if ($count < 1) return;

        $clients = $this->bulkProductClientMode === 'specific'
            ? Client::query()->whereIn('id', $this->bulkProductClientIds)->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code'])
            : collect();

        $this->selectedProductsQuery()->select(['id', 'metadata'])->reorder('id')->chunkById(200, function ($products) use ($clients): void {
            foreach ($products as $product) {
                $metadata = (array) ($product->metadata ?? []);
                $metadata['client_availability'] = $this->bulkProductClientMode;
                if ($this->bulkProductClientMode === 'specific') {
                    $metadata['client_ids'] = $clients->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
                    $metadata['client_availability_labels'] = $clients->pluck('name')->values()->all();
                    $metadata['client_codes'] = $clients->pluck('code')->filter()->values()->all();
                } else {
                    unset($metadata['client_ids'], $metadata['client_availability_labels'], $metadata['client_codes']);
                }
                $product->metadata = $metadata;
                $product->save();
            }
        });

        $this->bulkProductPanel = null;
        $this->clearProductSelection();
        session()->flash('success', 'Client availability updated for '.number_format($count).' '.strtolower(\Illuminate\Support\Str::plural('product', $count)).'.');
    }

    public function applyBulkProductCategory(): void
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);
        $workspaceId = app(MasterDataService::class)->workspaceId();
        $data = $this->validate([
            'bulkProductMainCategory' => ['required', 'string', 'max:255'],
            'bulkProductCategoryId' => [
                'required', 'integer',
                Rule::exists('master_records', 'id')->where(fn ($q) => $q
                    ->where('workspace_id', $workspaceId)
                    ->where('type', 'product_category')
                    ->where('status', 'active')
                    ->whereNull('deleted_at')),
            ],
            'bulkProductSubcategory' => ['nullable', 'string', 'max:255'],
        ]);
        $category = MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->active()->findOrFail((int) $data['bulkProductCategoryId']);
        $main = trim((string) (data_get($category->metadata, 'main_category') ?: data_get($category->metadata, 'excel_main_category') ?: $data['bulkProductMainCategory']));
        $subcategory = trim((string) $data['bulkProductSubcategory']);
        if ($subcategory !== '') {
            $knownSubcategory = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_subcategory')
                ->active()
                ->where('parent_id', $category->id)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($subcategory)])
                ->exists();
            $legacySubcategory = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->where('parent_id', $category->id)
                ->get(['metadata'])
                ->contains(fn (MasterRecord $product) => mb_strtolower(trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category')))) === mb_strtolower($subcategory));
            if (! $knownSubcategory && ! $legacySubcategory) {
                $this->addError('bulkProductSubcategory', 'Select a valid subcategory for the chosen product category.');
                return;
            }
        }
        $count = $this->productSelectionCount();
        if ($count < 1) return;

        $this->selectedProductsQuery()->select(['id', 'metadata'])->reorder('id')->chunkById(200, function ($products) use ($category, $main, $subcategory): void {
            foreach ($products as $product) {
                $metadata = (array) ($product->metadata ?? []);
                $metadata['main_category'] = $main;
                $metadata['excel_main_category'] = $main;
                unset($metadata['taxonomy_unassigned']);
                if ($subcategory !== '') {
                    $metadata['sub_category'] = $subcategory;
                    $metadata['excel_sub_category'] = $subcategory;
                } else {
                    unset($metadata['sub_category'], $metadata['excel_sub_category']);
                }
                $product->parent_id = $category->id;
                $product->metadata = $metadata;
                $product->save();
            }
        });

        $this->bulkProductPanel = null;
        $this->clearProductSelection();
        session()->flash('success', 'Category updated for '.number_format($count).' '.strtolower(\Illuminate\Support\Str::plural('product', $count)).'.');
    }

    public function exportSelectedProducts()
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'view'), 403);
        $count = $this->productSelectionCount();
        if ($count < 1) return null;
        $products = $this->selectedProductsQuery()->orderBy('id')->get();
        $filename = 'flowtrack-products-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($products): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Product code', 'Reference code', 'Product name', 'Main category', 'Product category', 'Subcategory', 'Size', 'Client availability', 'Status', 'Updated']);
            foreach ($products as $product) {
                fputcsv($out, [
                    $product->productDisplayCode(),
                    $product->productReferenceCode(),
                    $product->name,
                    $product->productMainCategory(),
                    $product->parent?->name,
                    trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category'))),
                    $product->productSize(),
                    implode(', ', $product->productAvailabilityLabels()),
                    ucfirst($product->status),
                    optional($product->updated_at)->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function bulkDeleteProducts(): void
    {
        abort_unless(auth()->user()?->canModule('catalog_products', 'delete'), 403);
        $products = $this->selectedProductsQuery()->get(['id', 'name']);
        if ($products->isEmpty()) return;
        $service = app(MasterDataService::class);
        $deleted = 0;
        $blocked = [];
        foreach ($products as $product) {
            try {
                $service->delete($product->id);
                $deleted++;
            } catch (ValidationException $exception) {
                $blocked[] = $product->name;
            }
        }
        $this->clearProductSelection();
        $this->resetPage('masterPage');
        if ($deleted) session()->flash('success', number_format($deleted).' '.strtolower(\Illuminate\Support\Str::plural('product', $deleted)).' deleted.');
        if ($blocked) $this->addError('record', count($blocked).' selected '.\Illuminate\Support\Str::plural('product', count($blocked)).' could not be deleted because they are in use.');
    }

    private function productSelectionCount(): int
    {
        if ($this->group !== 'product') return 0;
        if ($this->selectAllMatchingProducts) {
            return max(0, (int) $this->filteredProductsQuery()->count() - count($this->excludedProductIds));
        }
        return count(collect($this->selectedProductIds)->map(fn ($id) => (int) $id)->filter()->unique());
    }

    public function viewProduct(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'view'), 403);

        MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product')
            ->findOrFail($id);

        $this->viewProductId = $id;
        $this->showProductView = true;
    }

    public function closeProductView(): void
    {
        $this->showProductView = false;
        $this->viewProductId = null;
    }

    public function toggleProductStatus(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);

        $service = app(MasterDataService::class);
        $product = MasterRecord::query()
            ->forWorkspace($service->workspaceId())
            ->ofType('product')
            ->findOrFail($id);

        $nextStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product = $service->save('product', [
            'code' => $product->code,
            'name' => $product->name,
            'description' => $product->description,
            'parent_id' => $product->parent_id,
            'status' => $nextStatus,
            'sort_order' => $product->sort_order,
            'metadata' => $product->metadata,
        ], $product->id);
        $this->recordsReady = true;
        session()->flash('success', $product->status === 'active' ? 'Product activated.' : 'Product deactivated.');
    }

    public function editProduct(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);

        $this->showProductView = false;
        $this->viewProductId = null;

        // Resolve the row inside the active workspace/type before opening the
        // editor. This prevents a stale/tampered action id from ever opening a
        // different Master Data record.
        MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product')
            ->findOrFail($id);

        $this->open($id);
    }

    public function deleteProduct(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'delete'), 403);

        $service = app(MasterDataService::class);
        $product = MasterRecord::query()
            ->forWorkspace($service->workspaceId())
            ->ofType('product')
            ->findOrFail($id);

        $this->recordsReady = true;
        $this->resetValidation('record');

        try {
            $productName = $product->name;
            $service->delete($product->id);
            $this->resetPage('masterPage');
            session()->flash('success', 'Product deleted.');
            app(\App\Services\NotificationService::class)->notifyUser(
                auth()->user(),
                'Product deleted',
                $productName.' was removed from the product catalogue.',
                'update',
                null,
                null,
                auth()->user(),
            );
        } catch (ValidationException $e) {
            $this->addError('record', collect($e->errors())->flatten()->first());
        }
    }

    public function updateColor(int $id, string $color): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        app(MasterDataService::class)->setColor($record->id, $color);
    }

    public function toggle(int $id): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        app(MasterDataService::class)->toggle($record->id);
        session()->flash('success', 'Master data status updated.');
        app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data status updated', $record->name.' status was changed.', 'update', null, null, auth()->user());
    }

    public function deleteRecord(int $id): void
    {
        $this->recordsReady = true;
        $record = $this->currentGroupRecord($id);
        try {
            app(MasterDataService::class)->delete($record->id);
            $this->resetPage('masterPage');
            session()->flash('success', 'Master data record deleted.');
            app(\App\Services\NotificationService::class)->notifyUser(auth()->user(), 'Master data deleted', 'A master data record was deleted.', 'update', null, null, auth()->user());
        } catch (ValidationException $e) {
            $this->addError('record', collect($e->errors())->flatten()->first());
        }
    }

    private function authorizeGroupAction(string $action, ?string $group = null): void
    {
        $group ??= $this->group;
        abort_unless(array_key_exists($group, MasterDataService::LABELS), 404);
        $module = MasterDataService::permissionModuleForType($group);
        abort_unless(auth()->user()?->canModule($module, $action), 403);
    }

    private function currentGroupRecord(int $id): MasterRecord
    {
        return MasterRecord::query()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType($this->group)
            ->findOrFail($id);
    }

    public function render()
    {
        $this->authorizeGroupAction('view');
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $summaries = MasterRecord::query()
            ->where('workspace_id', $workspaceId)
            ->where('type', $this->group)
            ->selectRaw('type, count(*) as total_count')
            ->selectRaw("sum(case when status = 'active' then 1 else 0 end) as active_count")
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $rows = null;
        // Progressive rendering fallback retained for non-product groups: ? $service->paginate($this->group, $this->search, 30)
        if ($this->recordsReady) {
            if ($this->group === 'product') {
                $rows = $service->paginate($this->group, $this->search, $this->productPerPage, [
                    'main_category' => $this->productMainCategory,
                    'parent_id' => $this->productCategory,
                    'client_availability' => $this->productClientAvailability,
                    'status' => $this->productStatus,
                ]);
            } else {
                $rows = $service->paginate($this->group, $this->search, 30);
            }
        }

        $selected = $summaries->get($this->group);

        $parents = $this->showModal && $this->group === 'product'
            ? $service->active('product_category')
            : ($this->showModal && $this->group === 'state' ? $service->active('country') : collect());

        // Create/Edit Product must consume the exact same canonical taxonomy as
        // the Product Categories page. Do not infer child categories from
        // products: newly-created Product Categories/Subcategories may have zero
        // products and still need to be immediately selectable.
        $productTaxonomy = $this->group === 'product'
            ? app(\App\Services\ProductTaxonomyService::class)
            : null;
        $canonicalMainCategories = $productTaxonomy ? $productTaxonomy->mainCategories(true) : collect();
        $canonicalProductCategories = $productTaxonomy ? $productTaxonomy->productCategories(true) : collect();

        $productFormCategories = $canonicalProductCategories;
        if ($this->group === 'product' && $this->showModal && trim($this->productFormMainCategory) !== '') {
            $mainNeedle = mb_strtolower(trim($this->productFormMainCategory));
            $selectedMain = $canonicalMainCategories
                ->first(fn (MasterRecord $main) => mb_strtolower(trim($main->name)) === $mainNeedle);

            $productFormCategories = $canonicalProductCategories
                ->filter(function (MasterRecord $category) use ($productTaxonomy, $selectedMain, $mainNeedle): bool {
                    $main = $productTaxonomy?->mainCategoryFor($category);
                    if ($selectedMain && $main) {
                        return (int) $main->id === (int) $selectedMain->id;
                    }

                    // Legacy fallback only; synchronizeLegacyTaxonomy() normally
                    // gives every Product Category a canonical main_category_id.
                    $legacyMain = trim((string) (
                        data_get($category->metadata, 'main_category')
                        ?: data_get($category->metadata, 'excel_main_category')
                    ));
                    return $legacyMain !== '' && mb_strtolower($legacyMain) === $mainNeedle;
                })
                ->values();

            if ($this->parentId && !$productFormCategories->contains('id', (int) $this->parentId)) {
                $selectedCategory = $canonicalProductCategories->firstWhere('id', (int) $this->parentId);
                if ($selectedCategory) $productFormCategories->push($selectedCategory);
            }
        }

        $categoryMatches = collect();
        $similarCategories = collect();
        $hasExactCategory = false;
        $productCodeDuplicate = null;

        if ($this->showModal && $this->group === 'product' && !$this->editId) {
            $manualCode = trim($this->code);
            if ($manualCode !== '') {
                $productCodeDuplicate = MasterRecord::withTrashed()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->whereRaw('LOWER(code) = ?', [mb_strtolower($manualCode)])
                    ->first(['id', 'code', 'name', 'status', 'deleted_at']);
            }

            $needle = mb_strtolower(trim($this->productCategorySearch));
            if ($needle !== '') {
                $hasExactCategory = $parents->contains(fn (MasterRecord $category) => mb_strtolower($category->name) === $needle);
                $categoryMatches = $parents
                    ->filter(fn (MasterRecord $category) => str_contains(mb_strtolower($category->name), $needle))
                    ->take(6)
                    ->values();

                $matchedIds = $categoryMatches->pluck('id')->all();
                $similarCategories = $parents
                    ->reject(fn (MasterRecord $category) => in_array($category->id, $matchedIds, true))
                    ->sortBy(fn (MasterRecord $category) => levenshtein($needle, mb_strtolower($category->name)))
                    ->take(2)
                    ->values();
            }
        }

        // Keep two representations of main categories:
        // - records for native <select> filters on the Products list
        // - lightweight option arrays for the shared searchable selector used
        //   by Create/Edit Product and bulk actions. Mixing the two shapes caused
        //   Blade to try to escape an array as a string.
        $productMainCategoryFilterOptions = $this->group === 'product'
            ? $canonicalMainCategories
            : collect();
        $productMainCategories = $this->group === 'product'
            ? $canonicalMainCategories->map(fn (MasterRecord $main) => [
                'id' => $main->name,
                'label' => $main->name,
                'meta' => $main->code,
            ])->values()
            : collect();
        $productSubcategories = collect();
        if ($this->group === 'product') {
            $selectedCategoryId = (int) ($this->parentId ?? 0);

            $cataloguedSubcategories = $productTaxonomy->subcategories(true)
                ->when($selectedCategoryId > 0, fn ($items) => $items->where('parent_id', $selectedCategoryId))
                ->pluck('name');

            // Keep legacy product metadata visible during the transition, while
            // canonical Product Categories remains the source of truth.
            $legacySubcategories = $selectedCategoryId > 0
                ? MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->where('parent_id', $selectedCategoryId)
                    ->get(['metadata'])
                    ->map(fn (MasterRecord $product) => trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category'))))
                : collect();

            $productSubcategories = $cataloguedSubcategories
                ->concat($legacySubcategories)
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique(fn ($value) => mb_strtolower($value))
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        }

        $editProduct = null;
        if ($this->group === 'product' && $this->showModal && $this->editId) {
            $editProduct = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->with(['parent', 'creator'])
                ->find($this->editId);
        }

        $viewProduct = null;
        if ($this->group === 'product' && $this->showProductView && $this->viewProductId) {
            $viewProduct = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->with(['parent', 'creator'])
                ->find($this->viewProductId);
            if (! $viewProduct) {
                $this->showProductView = false;
                $this->viewProductId = null;
            }
        }

        $categoryMainPage = null;
        $categoryProductChildren = collect();
        $categorySubcategoryChildren = collect();
        $categoryMainCategories = collect();
        $categoryProductCategories = collect();
        $categorySubcategories = collect();
        $categoryParentOptions = collect();
        $categoryCounts = ['main' => 0, 'product' => 0, 'sub' => 0];
        $categoryProductCounts = collect();
        $categoryMainProductCounts = collect();
        $categorySubcategoryProductCounts = collect();
        $categoryProductChildTotals = collect();
        $categorySubcategoryChildTotals = collect();

        if ($this->group === 'product_category' && $this->recordsReady) {
            // Match the category lazy-pagination prototype exactly:
            // - only Main Categories are page-paginated;
            // - Product Category rows are queried only after their Main Category opens;
            // - Subcategory rows are queried only after their Product Category opens;
            // - child counts are lightweight count/id queries and do not hydrate child rows.
            $categoryMainCategories = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_main_category')
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'code', 'name', 'status', 'sort_order', 'updated_at']);

            $categoryCounts = [
                'main' => $categoryMainCategories->count(),
                'product' => (int) MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_category')->count(),
                'sub' => (int) MasterRecord::query()->forWorkspace($workspaceId)->ofType('product_subcategory')->count(),
            ];

            $mainIdByName = $categoryMainCategories
                ->mapWithKeys(fn (MasterRecord $main) => [mb_strtolower(trim((string) $main->name)) => (int) $main->id]);
            $mainNameById = $categoryMainCategories
                ->mapWithKeys(fn (MasterRecord $main) => [(int) $main->id => (string) $main->name]);

            $baseProductCategoryQuery = static fn () => MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_category');

            $applyProductMain = static function ($query, int $mainId, string $mainName) {
                return $query->where(function ($q) use ($mainId, $mainName) {
                    $q->where('metadata->main_category_id', $mainId)
                        ->orWhere('metadata->main_category', $mainName)
                        ->orWhere('metadata->excel_main_category', $mainName);
                });
            };

            $productMainId = static function (MasterRecord $category) use ($mainIdByName): int {
                $mainId = (int) data_get($category->metadata, 'main_category_id', 0);
                if ($mainId > 0) return $mainId;
                $legacyMain = mb_strtolower(trim((string) (
                    data_get($category->metadata, 'main_category')
                    ?: data_get($category->metadata, 'excel_main_category')
                )));
                return (int) ($mainIdByName[$legacyMain] ?? 0);
            };

            // The Parent filter is independent from table expansion. Load only
            // identifiers/names for Product Category options, never table rows.
            $productParentOptions = $baseProductCategoryQuery()
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'name']);
            $categoryParentOptions = $categoryMainCategories->map(fn (MasterRecord $main) => [
                'value' => 'main:'.$main->id,
                'label' => $main->name,
                'meta' => 'Main category',
            ])->concat($productParentOptions->map(fn (MasterRecord $category) => [
                'value' => 'product:'.$category->id,
                'label' => $category->name,
                'meta' => 'Product category',
            ]))->values();

            // Only the Subcategory editor needs the complete Product Category
            // collection. The normal hierarchy list never preloads it.
            if ($this->categoryEditorLevel === 'sub') {
                $categoryProductCategories = $baseProductCategoryQuery()
                    ->orderBy('sort_order')->orderBy('name')
                    ->get(['id', 'code', 'name', 'status', 'sort_order', 'metadata']);
            }

            $searchNeedle = trim($this->search);
            $levelFilter = trim($this->categoryLevelFilter);
            $statusFilter = trim($this->categoryStatusFilter);
            [$parentFilterType, $parentFilterId] = str_contains($this->categoryParentFilter, ':')
                ? array_pad(explode(':', $this->categoryParentFilter, 2), 2, '')
                : ['', ''];
            $parentFilterId = (int) $parentFilterId;

            $directMainIds = collect();
            if (in_array($levelFilter, ['', 'main'], true)) {
                $directMainQuery = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_main_category')
                    ->when($searchNeedle !== '', fn ($query) => $query->where(function ($q) use ($searchNeedle) {
                        $q->whereLike('name', '%'.$searchNeedle.'%')
                            ->orWhereLike('code', '%'.$searchNeedle.'%');
                    }))
                    ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter));

                if ($parentFilterType === 'main' && $parentFilterId > 0) {
                    $directMainQuery->whereKey($parentFilterId);
                } elseif ($parentFilterType === 'product' && $parentFilterId > 0) {
                    $directMainQuery->whereRaw('1 = 0');
                }

                $directMainIds = $directMainQuery->pluck('id')->map(fn ($id) => (int) $id)->values();
            }

            $directProductIds = collect();
            $needDirectProducts = $levelFilter === 'product'
                || ($levelFilter === '' && ($searchNeedle !== '' || $statusFilter !== '' || $parentFilterType === 'product'));
            if ($needDirectProducts) {
                $directProductQuery = $baseProductCategoryQuery()
                    ->when($searchNeedle !== '', fn ($query) => $query->where(function ($q) use ($searchNeedle) {
                        $q->whereLike('name', '%'.$searchNeedle.'%')
                            ->orWhereLike('code', '%'.$searchNeedle.'%');
                    }))
                    ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter));

                if ($parentFilterType === 'main' && $parentFilterId > 0) {
                    $mainName = (string) ($mainNameById[$parentFilterId] ?? '');
                    $applyProductMain($directProductQuery, $parentFilterId, $mainName);
                } elseif ($parentFilterType === 'product' && $parentFilterId > 0) {
                    $directProductQuery->whereKey($parentFilterId);
                }

                $directProductIds = $directProductQuery->pluck('id')->map(fn ($id) => (int) $id)->values();
            }

            $matchingSubParentIds = collect();
            $needSubMatches = $levelFilter === 'sub'
                || ($levelFilter === '' && ($searchNeedle !== '' || $statusFilter !== ''));
            if ($needSubMatches) {
                $subMatchQuery = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_subcategory')
                    ->when($searchNeedle !== '', fn ($query) => $query->where(function ($q) use ($searchNeedle) {
                        $q->whereLike('name', '%'.$searchNeedle.'%')
                            ->orWhereLike('code', '%'.$searchNeedle.'%');
                    }))
                    ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter));

                if ($parentFilterType === 'product' && $parentFilterId > 0) {
                    $subMatchQuery->where('parent_id', $parentFilterId);
                } elseif ($parentFilterType === 'main' && $parentFilterId > 0) {
                    $mainName = (string) ($mainNameById[$parentFilterId] ?? '');
                    $productIdsForMain = $applyProductMain($baseProductCategoryQuery(), $parentFilterId, $mainName)->pluck('id');
                    $productIdsForMain->isNotEmpty()
                        ? $subMatchQuery->whereIn('parent_id', $productIdsForMain->all())
                        : $subMatchQuery->whereRaw('1 = 0');
                }

                $matchingSubParentIds = $subMatchQuery
                    ->pluck('parent_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
            }

            // null means all Product Categories are eligible under a visible Main
            // Category. A Collection means restrict child queries to those IDs.
            $visibleProductIds = match ($levelFilter) {
                'main' => collect(),
                'product' => $directProductIds,
                'sub' => $matchingSubParentIds,
                default => ($searchNeedle === '' && $statusFilter === '' && $parentFilterType !== 'product')
                    ? null
                    : $directProductIds->concat($matchingSubParentIds)->unique()->values(),
            };
            if ($levelFilter === '' && $parentFilterType === 'product' && $parentFilterId > 0 && $searchNeedle === '' && $statusFilter === '') {
                $visibleProductIds = collect([$parentFilterId]);
            }

            $mainIdsFromProducts = collect();
            if ($visibleProductIds instanceof \Illuminate\Support\Collection && $visibleProductIds->isNotEmpty()) {
                $mainIdsFromProducts = $baseProductCategoryQuery()
                    ->whereIn('id', $visibleProductIds->all())
                    ->get(['id', 'metadata'])
                    ->map(fn (MasterRecord $category) => $productMainId($category))
                    ->filter()->unique()->values();
            }

            $noFilters = $levelFilter === '' && $searchNeedle === '' && $statusFilter === '' && $parentFilterType === '';
            $visibleMainIds = $noFilters
                ? null
                : match ($levelFilter) {
                    'main' => $directMainIds,
                    'product', 'sub' => $mainIdsFromProducts,
                    default => $directMainIds->concat($mainIdsFromProducts)->unique()->values(),
                };

            $mainPageQuery = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_main_category')
                ->orderBy('sort_order')->orderBy('name');
            if ($visibleMainIds instanceof \Illuminate\Support\Collection) {
                $visibleMainIds->isNotEmpty()
                    ? $mainPageQuery->whereIn('id', $visibleMainIds->all())
                    : $mainPageQuery->whereRaw('1 = 0');
            }

            $categoryMainPage = $mainPageQuery->paginate(
                max(1, $this->categoryPerPage),
                ['id', 'code', 'name', 'status', 'sort_order', 'updated_at'],
                'masterPage'
            );

            $pageMainIds = collect($categoryMainPage->items())->pluck('id')->map(fn ($id) => (int) $id)->values();

            // Main Category product totals are available while collapsed without
            // hydrating Product Category rows. Only child IDs for the six/selected
            // Main Categories on the current page are used for aggregation.
            foreach ($categoryMainPage->items() as $main) {
                $mainId = (int) $main->id;
                $mainName = (string) $main->name;

                $allCategoryIdsForMain = $applyProductMain($baseProductCategoryQuery(), $mainId, $mainName)
                    ->pluck('id')->map(fn ($id) => (int) $id)->values();
                $mainProductCount = 0;
                if ($allCategoryIdsForMain->isNotEmpty()) {
                    $mainProductCount += (int) MasterRecord::query()
                        ->forWorkspace($workspaceId)
                        ->ofType('product')
                        ->whereIn('parent_id', $allCategoryIdsForMain->all())
                        ->count();
                }
                $mainProductCount += (int) MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->whereNull('parent_id')
                    ->where(function ($query) use ($mainName) {
                        $query->where('metadata->main_category', $mainName)
                            ->orWhere('metadata->excel_main_category', $mainName);
                    })
                    ->count();
                $categoryMainProductCounts[mb_strtolower($mainName)] = $mainProductCount;

                if ($levelFilter === 'main') {
                    $categoryProductChildTotals[$mainId] = 0;
                    continue;
                }

                $childCountQuery = $applyProductMain($baseProductCategoryQuery(), $mainId, $mainName);
                if ($visibleProductIds instanceof \Illuminate\Support\Collection) {
                    $visibleProductIds->isNotEmpty()
                        ? $childCountQuery->whereIn('id', $visibleProductIds->all())
                        : $childCountQuery->whereRaw('1 = 0');
                }
                $categoryProductChildTotals[$mainId] = (int) $childCountQuery->count();
            }

            // Product Category rows are the first true lazy level: exactly four
            // are fetched on expand and four more on each Load more click.
            $loadedProductIds = collect();
            $expandedMainIds = collect($this->expandedMainCategoryIds)->map(fn ($id) => (int) $id);
            foreach ($categoryMainPage->items() as $main) {
                $mainId = (int) $main->id;
                if (! $expandedMainIds->contains($mainId) || $levelFilter === 'main') continue;

                $mainName = (string) $main->name;
                $limit = max(self::CATEGORY_PRODUCT_BATCH, (int) ($this->categoryProductLimits[$mainId] ?? self::CATEGORY_PRODUCT_BATCH));
                $childQuery = $applyProductMain($baseProductCategoryQuery(), $mainId, $mainName);
                if ($visibleProductIds instanceof \Illuminate\Support\Collection) {
                    $visibleProductIds->isNotEmpty()
                        ? $childQuery->whereIn('id', $visibleProductIds->all())
                        : $childQuery->whereRaw('1 = 0');
                }

                $rowsForMain = $childQuery
                    ->orderBy('sort_order')->orderBy('name')
                    ->limit($limit)
                    ->get(['id', 'code', 'name', 'status', 'sort_order', 'metadata', 'updated_at']);
                $categoryProductChildren[$mainId] = $rowsForMain;
                $loadedProductIds = $loadedProductIds->concat($rowsForMain->pluck('id')->map(fn ($id) => (int) $id));
            }
            $loadedProductIds = $loadedProductIds->unique()->values();

            if ($loadedProductIds->isNotEmpty()) {
                $categoryProductCounts = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->whereIn('parent_id', $loadedProductIds->all())
                    ->selectRaw('parent_id, COUNT(*) as aggregate')
                    ->groupBy('parent_id')
                    ->pluck('aggregate', 'parent_id')
                    ->map(fn ($count) => (int) $count);
            }

            // Child totals for the loaded Product Category rows use grouped count
            // queries only. No Subcategory row is fetched until the row expands.
            if ($loadedProductIds->isNotEmpty() && in_array($levelFilter, ['', 'sub'], true)) {
                $subCountQuery = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_subcategory')
                    ->whereIn('parent_id', $loadedProductIds->all())
                    ->when($searchNeedle !== '', fn ($query) => $query->where(function ($q) use ($searchNeedle) {
                        $q->whereLike('name', '%'.$searchNeedle.'%')
                            ->orWhereLike('code', '%'.$searchNeedle.'%');
                    }))
                    ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter));
                if ($parentFilterType === 'product' && $parentFilterId > 0) {
                    $subCountQuery->where('parent_id', $parentFilterId);
                }
                $categorySubcategoryChildTotals = $subCountQuery
                    ->selectRaw('parent_id, COUNT(*) as aggregate')
                    ->groupBy('parent_id')
                    ->pluck('aggregate', 'parent_id')
                    ->map(fn ($count) => (int) $count);
            }

            // Subcategory rows are the second true lazy level: exactly three are
            // fetched on expand and three more on each Load more click.
            $expandedProductIds = collect($this->expandedProductCategoryIds)->map(fn ($id) => (int) $id);
            foreach ($loadedProductIds as $productCategoryId) {
                $productCategoryId = (int) $productCategoryId;
                if (! $expandedProductIds->contains($productCategoryId)) continue;
                if (! in_array($levelFilter, ['', 'sub'], true)) continue;
                if ($parentFilterType === 'product' && $parentFilterId > 0 && $productCategoryId !== $parentFilterId) continue;

                $limit = max(self::CATEGORY_SUBCATEGORY_BATCH, (int) ($this->categorySubcategoryLimits[$productCategoryId] ?? self::CATEGORY_SUBCATEGORY_BATCH));
                $subQuery = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_subcategory')
                    ->where('parent_id', $productCategoryId)
                    ->when($searchNeedle !== '', fn ($query) => $query->where(function ($q) use ($searchNeedle) {
                        $q->whereLike('name', '%'.$searchNeedle.'%')
                            ->orWhereLike('code', '%'.$searchNeedle.'%');
                    }))
                    ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter))
                    ->orderBy('sort_order')->orderBy('name');

                $categorySubcategoryChildren[$productCategoryId] = $subQuery
                    ->limit($limit)
                    ->get(['id', 'parent_id', 'code', 'name', 'status', 'sort_order', 'updated_at']);
            }

            // Product counts for visible Subcategory rows are calculated only
            // after their Product Category has been expanded.
            $expandedLoadedIds = $expandedProductIds->intersect($loadedProductIds)->values();
            if ($expandedLoadedIds->isNotEmpty()) {
                $visibleSubNamesByProduct = collect($categorySubcategoryChildren)->map(
                    fn ($subs) => collect($subs)->map(fn (MasterRecord $sub) => mb_strtolower(trim((string) $sub->name)))->flip()
                );
                MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->whereIn('parent_id', $expandedLoadedIds->all())
                    ->select(['parent_id', 'metadata'])
                    ->chunk(500, function ($products) use (&$categorySubcategoryProductCounts, $visibleSubNamesByProduct): void {
                        foreach ($products as $product) {
                            $parentId = (int) $product->parent_id;
                            $sub = mb_strtolower(trim((string) (
                                data_get($product->metadata, 'sub_category')
                                ?: data_get($product->metadata, 'excel_sub_category')
                            )));
                            if ($sub === '' || ! $visibleSubNamesByProduct->get($parentId, collect())->has($sub)) continue;
                            $key = $parentId.'|'.$sub;
                            $categorySubcategoryProductCounts[$key] = ((int) ($categorySubcategoryProductCounts[$key] ?? 0)) + 1;
                        }
                    });
            }

            // Only the open Subcategory editor needs the full Subcategory list.
            if ($this->categoryEditorLevel === 'sub') {
                $categorySubcategories = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_subcategory')
                    ->orderBy('sort_order')->orderBy('name')
                    ->get(['id', 'parent_id', 'code', 'name', 'status', 'sort_order']);
            }
        }
        $productSelectionCount = $this->group === 'product' ? $this->productSelectionCount() : 0;
        $categorySelectionCount = $this->group === 'product_category' ? count($this->selectedCategoryKeys) : 0;
        $bulkProductCategories = collect();
        $bulkProductSubcategories = collect();
        if ($this->group === 'product' && $this->bulkProductPanel === 'category') {
            $bulkMainNeedle = mb_strtolower(trim($this->bulkProductMainCategory));
            $bulkCategoryIdsFromProducts = $bulkMainNeedle === '' ? [] : MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->get(['parent_id', 'metadata'])
                ->filter(fn (MasterRecord $product) => mb_strtolower($product->productMainCategory()) === $bulkMainNeedle)
                ->pluck('parent_id')->filter()->map(fn ($id) => (int) $id)->unique()->all();

            $bulkProductCategories = $service->active('product_category')
                ->filter(function (MasterRecord $category) use ($bulkMainNeedle, $bulkCategoryIdsFromProducts): bool {
                    if ($bulkMainNeedle === '') return true;
                    $main = trim((string) (data_get($category->metadata, 'main_category') ?: data_get($category->metadata, 'excel_main_category')));
                    return in_array((int) $category->id, $bulkCategoryIdsFromProducts, true)
                        || mb_strtolower($main) === $bulkMainNeedle
                        || mb_strtolower(trim((string) $category->name)) === $bulkMainNeedle;
                })->values();

            if ($this->bulkProductCategoryId) {
                $cataloguedBulkSubcategories = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product_subcategory')
                    ->active()
                    ->where('parent_id', $this->bulkProductCategoryId)
                    ->orderBy('sort_order')->orderBy('name')->pluck('name');
                $legacyBulkSubcategories = MasterRecord::query()
                    ->forWorkspace($workspaceId)
                    ->ofType('product')
                    ->where('parent_id', $this->bulkProductCategoryId)
                    ->get(['metadata'])
                    ->map(fn (MasterRecord $product) => trim((string) (data_get($product->metadata, 'sub_category') ?: data_get($product->metadata, 'excel_sub_category'))));
                $bulkProductSubcategories = $cataloguedBulkSubcategories->concat($legacyBulkSubcategories)
                    ->map(fn ($value) => trim((string) $value))->filter()
                    ->unique(fn ($value) => mb_strtolower($value))->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
            }
        }

        $orderTaskFlagOptions = $this->group === 'order_task_status'
            ? $service->active('order_task_flag')
                ->reject(fn (MasterRecord $flag) => $flag->systemKey() === 'overdue')
                ->values()
            : collect();
        $orderFlagOptions = $this->group === 'order_task_flag'
            ? $service->active('order_flag')
            : collect();

        return view('livewire.master-data.index', [
            'labels' => MasterDataService::LABELS,
            'rows' => $rows,
            'parents' => $parents,
            'categoryMatches' => $categoryMatches,
            'similarCategories' => $similarCategories,
            'hasExactCategory' => $hasExactCategory,
            'productCodeDuplicate' => $productCodeDuplicate,
            'productCategories' => $this->group === 'product' ? $service->list('product_category') : collect(),
            'productFormCategories' => $productFormCategories,
            'productMainCategories' => $productMainCategories,
            'productMainCategoryFilterOptions' => $productMainCategoryFilterOptions,
            'productSubcategories' => $productSubcategories,
            'productClients' => $this->group === 'product' ? Client::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']) : collect(),
            'viewProduct' => $viewProduct,
            'editProduct' => $editProduct,
            'productSelectionCount' => $productSelectionCount,
            'categorySelectionCount' => $categorySelectionCount,
            'bulkProductCategories' => $bulkProductCategories,
            'bulkProductSubcategories' => $bulkProductSubcategories,
            'categoryMainPage' => $categoryMainPage,
            'categoryProductChildren' => $categoryProductChildren,
            'categorySubcategoryChildren' => $categorySubcategoryChildren,
            'categoryMainCategories' => $categoryMainCategories,
            'categoryProductCategories' => $categoryProductCategories,
            'categorySubcategories' => $categorySubcategories,
            'categoryParentOptions' => $categoryParentOptions,
            'categoryCounts' => $categoryCounts,
            'categoryProductCounts' => $categoryProductCounts,
            'categoryMainProductCounts' => $categoryMainProductCounts,
            'categorySubcategoryProductCounts' => $categorySubcategoryProductCounts,
            'categoryProductChildTotals' => $categoryProductChildTotals,
            'categorySubcategoryChildTotals' => $categorySubcategoryChildTotals,
            'groupCounts' => collect(MasterDataService::LABELS)->mapWithKeys(
                fn ($label, $type) => [$type => (int) ($summaries->get($type)?->total_count ?? 0)]
            ),
            'total' => (int) $summaries->sum('total_count'),
            'active' => (int) $summaries->sum('active_count'),
            'selectedTotal' => (int) ($selected?->total_count ?? 0),
            'selectedActive' => (int) ($selected?->active_count ?? 0),
            'orderTaskFlagOptions' => $orderTaskFlagOptions,
            'orderFlagOptions' => $orderFlagOptions,
        ]);
    }
}
