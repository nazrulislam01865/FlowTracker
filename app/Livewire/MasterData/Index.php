<?php

namespace App\Livewire\MasterData;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Models\MasterRecord;
use App\Services\MasterDataService;
use App\Services\ProductImageService;
use App\Support\MasterColor;
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
    public string $productCategory = '';
    public string $productStatus = '';
    public int $productPerPage = 10;
    public bool $recordsReady = false;
    public bool $showModal = false;
    public ?int $editId = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $color = '#2563EB';
    public ?int $parentId = null;
    public string $productCategorySearch = '';
    public string $newProductCategoryName = '';
    public string $status = 'active';
    public int $sortOrder = 0;
    public string $metadataJson = '';
    public $productImage = null;
    public ?string $existingProductImageUrl = null;
    public bool $removeProductImage = false;

    public function mount(): void
    {
        if (!array_key_exists($this->group, MasterDataService::LABELS)) {
            $this->group = 'product';
        }

        $this->authorizeGroupAction('view');
    }

    public function selectGroup(string $group): void
    {
        abort_unless(array_key_exists($group, MasterDataService::LABELS), 404);
        $this->authorizeGroupAction('view', $group);
        $this->group = $group;
        $this->recordsReady = true;
        $this->search = '';
        $this->productCategory = '';
        $this->productStatus = '';
        $this->parentId = null;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->resetPage('masterPage');
        $this->resetValidation();
    }

    public function open(?int $id = null): void
    {
        $action = $id ? 'edit' : 'create';
        $this->authorizeGroupAction($action);
        $service = app(MasterDataService::class);
        $this->recordsReady = true;
        $this->showModal = true;
        $this->editId = $id;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        $this->productCategorySearch = '';
        $this->newProductCategoryName = '';
        $this->resetValidation();

        if ($id) {
            $r = MasterRecord::where('workspace_id', $service->workspaceId())->findOrFail($id);
            abort_unless($r->type === $this->group, 404);
            $this->code = $r->code;
            $this->name = $r->name;
            $this->description = (string) $r->description;
            $this->color = MasterColor::normalize($r->color) ?: MasterColor::defaultFor($this->group, $r->name);
            $this->parentId = in_array($this->group, ['product', 'state'], true) ? $r->parent_id : null;
            if ($this->group === 'product' && $r->parent_id) {
                $this->productCategorySearch = (string) $r->parent?->name;
            }
            $this->status = $r->status;
            $this->sortOrder = (int) $r->sort_order;
            $this->existingProductImageUrl = $r->productImageUrl();
            $metadata = (array) ($r->metadata ?? []);
            unset($metadata['product_image_path']);
            $this->metadataJson = $metadata ? json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
            return;
        }

        $this->reset(['code', 'name', 'description', 'parentId', 'metadataJson']);
        $this->color = MasterColor::defaultFor($this->group);
        // Product SKU/code is entered manually by the user. Other Master Data
        // types keep their generated, locked system codes.
        $this->code = $this->group === 'product' ? '' : $service->nextCode($this->group);
        $this->status = 'active';
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
        $this->resetValidation();
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

    public function updatedCode(): void
    {
        if ($this->group === 'product' && $this->showModal && !$this->editId) {
            $this->code = strtoupper(trim($this->code));
            $this->resetValidation('code');
        }
    }

    private function productCodeReadyForCategory(): bool
    {
        $code = strtoupper(trim($this->code));
        $this->code = $code;

        if ($code === '') {
            $this->addError('code', 'Enter the SKU / product code first.');
            return false;
        }

        if (mb_strlen($code) > 40 || preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $code) !== 1) {
            $this->addError('code', 'Use letters, numbers, dots, dashes or underscores only. Maximum 40 characters.');
            return false;
        }

        $duplicate = MasterRecord::withTrashed()
            ->forWorkspace(app(MasterDataService::class)->workspaceId())
            ->ofType('product')
            ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)])
            ->first();

        if ($duplicate) {
            $this->addError('code', $duplicate->trashed()
                ? 'This product code is reserved by an archived product.'
                : 'This product code already exists.');
            return false;
        }

        $this->resetValidation('code');
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
        $this->newProductCategoryName = '';
        $this->resetValidation(['parentId', 'newProductCategoryName']);
        $this->dispatch('product-category-selected');
    }

    public function beginProductCategoryCreation(): void
    {
        abort_unless($this->group === 'product' && $this->showModal && !$this->editId, 404);
        abort_unless(auth()->user()?->canModule('product_categories', 'create'), 403);
        if (!$this->productCodeReadyForCategory()) return;

        $this->newProductCategoryName = trim($this->productCategorySearch);
        $this->resetValidation('newProductCategoryName');
    }

    public function cancelProductCategoryCreation(): void
    {
        $this->newProductCategoryName = '';
        $this->resetValidation('newProductCategoryName');
    }

    public function createProductCategory(): void
    {
        abort_unless($this->group === 'product' && $this->showModal && !$this->editId, 404);
        abort_unless(auth()->user()?->canModule('product_categories', 'create'), 403);
        if (!$this->productCodeReadyForCategory()) return;

        $name = trim($this->newProductCategoryName ?: $this->productCategorySearch);
        $this->newProductCategoryName = $name;
        $this->validate([
            'newProductCategoryName' => ['required', 'string', 'max:255'],
        ]);

        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();
        $existing = MasterRecord::query()
            ->forWorkspace($workspaceId)
            ->ofType('product_category')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            if ($existing->status !== 'active') {
                $this->addError('newProductCategoryName', 'A category with this name already exists but is inactive. Activate it from Product Categories first.');
                return;
            }

            $this->parentId = $existing->id;
            $this->productCategorySearch = $existing->name;
            $this->newProductCategoryName = '';
            $this->resetValidation(['parentId', 'newProductCategoryName']);
            $this->dispatch('product-category-created');
            return;
        }

        $category = $service->save('product_category', [
            'code' => $service->nextCode('product_category'),
            'name' => $name,
            'description' => null,
            'parent_id' => null,
            'status' => 'active',
            'sort_order' => (int) MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product_category')
                ->max('sort_order') + 1,
            'metadata' => null,
        ]);

        $this->parentId = $category->id;
        $this->productCategorySearch = $category->name;
        $this->newProductCategoryName = '';
        $this->resetValidation(['parentId', 'newProductCategoryName']);
        $this->dispatch('product-category-created');
    }

    public function updatedSearch(): void
    {
        $this->recordsReady = true;
        $this->resetPage('masterPage');
    }

    public function updatedProductCategory(): void
    {
        $this->recordsReady = true;
        $this->resetPage('masterPage');
    }

    public function updatedProductStatus(): void
    {
        $this->recordsReady = true;
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
        $this->productCategory = '';
        $this->productStatus = '';
        $this->recordsReady = true;
        $this->resetPage('masterPage');
    }

    public function loadMasterRecords(): void
    {
        $this->recordsReady = true;
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

    public function save(): void
    {
        $service = app(MasterDataService::class);
        $workspaceId = $service->workspaceId();

        // Refresh generated codes only for system-coded Master Data. Product
        // SKU/code is intentionally manual and must never be replaced here.
        if (!$this->editId && $this->group !== 'product') {
            $this->code = $service->nextCode($this->group);
        } elseif (!$this->editId && $this->group === 'product') {
            $this->code = strtoupper(trim($this->code));
        }

        $data = $this->validate([
            'code' => $this->group === 'product' && !$this->editId
                ? ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/']
                : ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'color' => in_array($this->group, MasterDataService::COLOR_TYPES, true)
                ? ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']
                : ['nullable'],
            'parentId' => match ($this->group) {
                'product' => [
                    $this->editId ? 'nullable' : 'required',
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
            'productImage' => $this->group === 'product'
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
                : ['nullable'],
        ]);

        $metadata = null;
        if (filled($data['metadataJson'])) {
            $metadata = json_decode($data['metadataJson'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($metadata)) {
                throw ValidationException::withMessages(['metadataJson' => 'Metadata must be valid JSON.']);
            }
        }

        if ($this->group === 'product' && $this->editId) {
            $existing = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('product')
                ->findOrFail($this->editId);
            $imagePath = trim((string) data_get($existing->metadata, 'product_image_path'));
            if ($imagePath !== '') {
                $metadata ??= [];
                $metadata['product_image_path'] = $imagePath;
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

        $this->showModal = false;
        $this->productImage = null;
        $this->existingProductImageUrl = null;
        $this->removeProductImage = false;
        session()->flash('success', 'Master data saved.');
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

    public function editProduct(int $id): void
    {
        abort_unless($this->group === 'product', 404);
        abort_unless(auth()->user()?->canModule('catalog_products', 'edit'), 403);

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
                    'parent_id' => $this->productCategory,
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

        return view('livewire.master-data.index', [
            'labels' => MasterDataService::LABELS,
            'rows' => $rows,
            'parents' => $parents,
            'categoryMatches' => $categoryMatches,
            'similarCategories' => $similarCategories,
            'hasExactCategory' => $hasExactCategory,
            'productCodeDuplicate' => $productCodeDuplicate,
            'productCategories' => $this->group === 'product' ? $service->list('product_category') : collect(),
            'groupCounts' => collect(MasterDataService::LABELS)->mapWithKeys(
                fn ($label, $type) => [$type => (int) ($summaries->get($type)?->total_count ?? 0)]
            ),
            'total' => (int) $summaries->sum('total_count'),
            'active' => (int) $summaries->sum('active_count'),
            'selectedTotal' => (int) ($selected?->total_count ?? 0),
            'selectedActive' => (int) ($selected?->active_count ?? 0),
        ]);
    }
}
