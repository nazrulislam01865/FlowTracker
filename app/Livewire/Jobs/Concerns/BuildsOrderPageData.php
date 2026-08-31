<?php

namespace App\Livewire\Jobs\Concerns;

use App\Queries\Inquiries\InquiryDetailQuery;
use App\Queries\Orders\OrderListQuery;
use App\Queries\Orders\VisibleOrderQuery;
use App\Models\ClientDeliveryContact;
use App\Models\ClientShippingAddress;
use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowPhase;
use App\Services\AccessControlService;
use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\MasterDataService;
use App\Services\OrderFinanceService;
use App\Services\OrderDetailViewService;
use App\Services\OrderRedoService;
use App\Services\OrderWorkflowSetupService;
use App\Services\TaskService;
use App\Services\WorkspaceSettingsService;
use App\Support\BoardLaneResolver;
use Throwable;

/**
 * Phase 5 Order UI workflow extracted from the legacy Jobs coordinator.
 *
 * Public method names and parent Livewire state are intentionally preserved so
 * existing Blade bindings, deep links, validation keys and realtime behavior do
 * not change during the incremental decomposition.
 */
trait BuildsOrderPageData
{
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
                ->with(['contacts' => fn ($query) => $query
                    ->select(['id', 'client_id', 'name', 'job_title', 'phone', 'is_primary', 'sort_order'])
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id')])
                ->get(['id', 'name', 'contact_name', 'contact_job_title', 'phone'])
            : collect();

        $savedShippingAddresses = $clients->isNotEmpty()
            ? ClientShippingAddress::query()
                ->where('client_id', $this->clientId)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'client_id', 'label', 'recipient', 'address_line1', 'suite', 'city', 'state', 'zip', 'country', 'is_default', 'sort_order'])
            : collect();

        $savedDeliveryContacts = $clients->isNotEmpty()
            ? ClientDeliveryContact::query()
                ->where('client_id', $this->clientId)
                ->whereIn('contact_type', ['end_customer', 'other_contact'])
                ->orderByDesc('last_used_at')
                ->orderByDesc('id')
                ->get(['id', 'client_id', 'contact_type', 'name', 'phone_country_code', 'phone', 'last_used_at'])
            : collect();

        $phoneCountryCodeOptions = $options->options(
            $user,
            'phone-country-codes',
            'create-job',
            '',
            $this->shippingPhoneCountryCode,
            5,
        );

        // Render Create Order from the shared Workflow Setup source of truth.
        // Only active, client-available and runtime-complete Order workflows are
        // selectable; Inquiry workflows remain separate.
        $workflows = $this->createWorkflowReady
            ? OrderWorkflowSetupService::orderWorkflowQuery()
                ->with([
                    'phases' => fn ($query) => $query->where('is_active', true)->orderBy('sequence'),
                    'phases.taskPack.items' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                ])
                ->where('is_active', true)
                ->availableFor('orders', $this->clientId ? (int) $this->clientId : null)
                ->orderByRaw("CASE WHEN client_availability = 'specific' THEN 0 ELSE 1 END")
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->get()
                ->filter(fn (WorkflowTemplate $workflow): bool => app(OrderWorkflowSetupService::class)->isReadyForOrderCreation((int) $workflow->id))
                ->values()
            : collect();

        $workspaceId = $master->workspaceId();
        $canUseOrderProductSelector = $this->canUseCreateOrderProducts($user);
        $canViewProductCategories = $user->canModule('product_categories', 'view');
        $productCategories = collect();
        $productSearchResults = collect();
        $selectedProductDetails = collect();
        $selectedProductSuppliers = collect();
        $productSearchSuppliers = collect();
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
            $resultLimit = $this->createProductShowAllResults ? 100 : 3;
            $productSearchResults = $catalog->searchForOrderCreation($search, $categoryFilterId ?: null, $resultLimit);
            $productSearchSuppliers = $catalog->suppliersForProducts($productSearchResults->keyBy('id'));

            $selectedProductDetails = $catalog->selectedProducts(
                collect($this->jobItems)->pluck('product_id')
            );
            $selectedProductSuppliers = $catalog->suppliersForSelectionRows($this->jobItems);
        }

        $duplicateProduct = null;
        $newProductCategoryMatches = collect();
        $newProductSimilarCategories = collect();
        $newProductSimilarProducts = collect();
        $newProductSelectedCategory = null;
        $newProductHasExactCategory = false;
        $newProductImagePreview = null;
        $newProductSupplierOptions = collect();

        if ($canUseOrderProductSelector && $this->showCreateOrderProductModal) {
            // This optional picker is intentionally local inside the modal. Loading
            // the canonical active Supplier Master Data rows up front avoids a
            // second remote-dropdown lifecycle fighting Livewire modal morphs, and
            // guarantees that clicking the field always opens the real supplier list.
            $newProductSupplierOptions = MasterRecord::query()
                ->forWorkspace($workspaceId)
                ->ofType('supplier')
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'code'])
                ->map(fn (MasterRecord $supplier) => [
                    'id' => (string) $supplier->id,
                    'label' => (string) $supplier->name,
                    'meta' => trim((string) $supplier->code),
                ])
                ->values();
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
            'savedShippingAddresses' => $savedShippingAddresses,
            'savedDeliveryContacts' => $savedDeliveryContacts,
            'phoneCountryCodeOptions' => $phoneCountryCodeOptions,
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
                ? $workflows->map(function (WorkflowTemplate $workflow): array {
                    $activePhases = $workflow->phases->where('is_active', true);
                    $taskCount = $activePhases->sum(fn (WorkflowPhase $phase) => $phase->taskPack?->items?->count() ?? 0);

                    return [
                        'id' => (int) $workflow->id,
                        'label' => (string) $workflow->name,
                        'meta' => $activePhases->count().' stages · '.$taskCount.' tasks · Workflow Setup',
                    ];
                })->values()
                : collect(),
            'productCategories' => $productCategories,
            'productSearchResults' => $productSearchResults,
            'productSearchSuppliers' => $productSearchSuppliers,
            'selectedProductDetails' => $selectedProductDetails,
            'selectedProductSuppliers' => $selectedProductSuppliers,
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
            'newProductSupplierOptions' => $newProductSupplierOptions,
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForCreate($user),
        ];
    }

    private function taskPageData(User $user): array
    {
        app(\App\Services\OrderTaskFlagService::class)->syncDueTransitions();
        $master = app(MasterDataService::class);

        // If an interaction forces a lazy section to hydrate, promote that
        // readiness into component state. Otherwise closing a modal / finishing
        // an upload can make the section disappear back into a skeleton until a
        // second IntersectionObserver request fires.
        if ($this->showTaskDocumentPicker || count($this->taskDocumentUploads) > 0) {
            $this->taskDetailSectionsReady['attachments'] = true;
        }
        if (filled($this->focusComment) || $this->taskActivityTab !== 'all' || $this->taskActivityPage > 1) {
            $this->taskDetailSectionsReady['activity'] = true;
        }

        $taskDetailSectionsReady = $this->taskDetailSectionsReady;

        $with = [
            'job.client:id,name,logo_path',
            'job.orderFlag:id,type,name,color,status,sort_order,metadata',
            'assignee',
            'phase',
            'orderTaskStatus:id,type,name,color,status,sort_order,metadata',
            'orderTaskFlag:id,type,name,color,status,sort_order,metadata',
            'documentCategory',
            'setupTemplate.documentCategory',
        ];

        if ($taskDetailSectionsReady['checklist']) {
            $with[] = 'checklistItems';
        }
        if ($taskDetailSectionsReady['attachments']) {
            $with[] = 'documents.uploader:id,name';
            $with[] = 'links.creator:id,name';
        }
        if ($taskDetailSectionsReady['activity']) {
            $with[] = 'comments.user';
            $with[] = 'activities.user';
        }

        $task = app(TaskService::class)->visibleQuery($user)
            ->with($with)
            ->findOrFail($this->selectedTaskId);

        $availableDocuments = $taskDetailSectionsReady['attachments'] && $this->showTaskDocumentPicker
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
            'taskDetailSectionsReady' => $taskDetailSectionsReady,
        ];
    }

    private function jobPageData(User $user): array
    {
        if (! in_array($this->detailTab, ['overview', 'inquiry', 'finance', 'redo'], true)) {
            $this->detailTab = 'overview';
        }

        $master = app(MasterDataService::class);
        $orderQuery = app(VisibleOrderQuery::class);
        $selected = $orderQuery->base($user, $this->selectedJobId);

        // The Redo surface exists only after a Redo/discount action has actually
        // been initiated. Interactive tab clicks are already guarded in
        // setDetailTab(); this also protects direct URLs such as ?tab=redo.
        $preloadedRedoContext = null;
        if ($this->detailTab === 'redo') {
            $preloadedRedoContext = app(OrderRedoService::class)->context($selected, $user);
            if (! (bool) ($preloadedRedoContext['hasRedo'] ?? false)) {
                $this->detailTab = 'overview';
            }
        }

        if ($this->detailTab === 'overview') {
            // Promote any interaction-forced section into persistent readiness.
            // This keeps the Order detail DOM stable after modals/forms close
            // instead of swapping a real section back to a skeleton.
            if ($this->showAddJobProductForm || $this->showEditOrderProductModal) {
                $this->orderDetailSectionsReady['products'] = true;
            }
            if ($this->showOverviewTaskDocumentModal || $this->showOrderWorkflowActionModal || $this->showAddOrderTaskForm) {
                $this->orderDetailSectionsReady['workflow'] = true;
            }
            if (filled($this->focusComment) || $this->jobActivityTab !== 'all' || $this->jobActivityPage > 1) {
                $this->orderDetailSectionsReady['activity'] = true;
            }
        }

        $orderDetailSectionsReady = $this->orderDetailSectionsReady;

        if ($this->detailTab === 'overview') {
            // Keep the page shell immediately usable, but hydrate expensive
            // lower sections only when their viewport placeholder is reached.
            $orderQuery->loadOverviewSummary($selected, $user);
            if ($orderDetailSectionsReady['products']) {
                $orderQuery->loadOverviewProducts($selected, $user);
            }
            if ($orderDetailSectionsReady['workflow']) {
                $orderQuery->loadOverviewWorkflow($selected, $user);
            }
            if ($orderDetailSectionsReady['attachments']) {
                $orderQuery->loadOverviewDocuments($selected);
            }
            if ($orderDetailSectionsReady['activity']) {
                $orderQuery->loadOverviewActivity(
                    $selected,
                    $this->jobActivityTab,
                    $this->jobActivityPage,
                    10,
                );
            }

            $selectedPhase = $selected->workflow?->phases?->firstWhere('id', (int) ($this->overviewPhaseId ?: 0));
            if (!$selectedPhase || (int) $selectedPhase->sequence > (int) ($selected->phase?->sequence ?? 0)) {
                $this->overviewPhaseId = (int) $selected->workflow_phase_id;
            }
        } else {
            $orderQuery->loadTab($selected, $user, $this->detailTab);
        }

        // Redo is an explicitly selected tab, so its activity is intentional
        // work and may load immediately. Overview activity stays viewport-lazy.
        if ($this->detailTab === 'redo') {
            $orderQuery->loadOverviewActivity(
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
                try {
                    app(InquiryDetailQuery::class)->find($user, (int) $selected->sourceInquiry->id);
                    $linkedInquiryCanOpen = true;
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $linkedInquiryCanOpen = false;
                }
            }

            if (!$selected->source_inquiry_id && $canManageInquiryLink && mb_strlen(trim($this->inquirySearch)) >= 2) {
                $inquiryResults = $orderQuery->inquiryLinkResults($user, $selected, $this->inquirySearch, 8);
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
        $jobProductSearchSuppliers = collect();
        $jobProductResultTotal = 0;
        $jobProductSelectedProduct = null;
        $jobProductSelectedSupplier = null;
        if ($this->detailTab === 'overview' && $orderDetailSectionsReady['products'] && $this->showAddJobProductForm) {
            $catalog = app(\App\Services\ProductCatalogService::class);
            $productSearch = trim($this->jobProductSearch);
            $jobProductResultTotal = $catalog->orderSearchCount($productSearch, null);
            $resultLimit = $this->jobProductShowAllResults || $jobProductResultTotal <= 20 ? 20 : 3;
            $jobProductSearchResults = $catalog->searchForOrderCreation($productSearch, null, $resultLimit);
            $jobProductSearchSuppliers = $catalog->suppliersForProducts($jobProductSearchResults->keyBy('id'));
            if ($this->jobProductSelectedId) {
                $jobProductSelectedProduct = $catalog->selectedProducts([$this->jobProductSelectedId])->first();
                $jobProductSelectedSupplier = $jobProductSelectedProduct
                    ? $catalog->supplierForProduct($jobProductSelectedProduct)
                    : null;
            }
        }

        $editOrderProductSearchResults = collect();
        $editOrderProductSearchSuppliers = collect();
        $editOrderProductResultTotal = 0;
        $editOrderProductSelectedProduct = null;
        $editOrderProductSelectedSupplier = null;
        if (
            $this->detailTab === 'overview'
            && $orderDetailSectionsReady['products']
            && $this->showEditOrderProductModal
            && $this->editOrderProductItemId
        ) {
            $catalog = app(\App\Services\ProductCatalogService::class);
            $productSearch = trim($this->editOrderProductSearch);
            $editOrderProductResultTotal = $catalog->orderSearchCount($productSearch, null);
            $resultLimit = $this->editOrderProductShowAllResults || $editOrderProductResultTotal <= 20 ? 20 : 3;
            $editOrderProductSearchResults = $catalog->searchForOrderCreation($productSearch, null, $resultLimit);
            $editOrderProductSearchSuppliers = $catalog->suppliersForProducts($editOrderProductSearchResults->keyBy('id'));
            if ($this->editOrderProductSelectedId) {
                $editOrderProductSelectedProduct = $catalog->selectedProducts([$this->editOrderProductSelectedId])->first();
            }
            if ($this->editOrderProductSupplierId) {
                $editOrderProductSelectedSupplier = \App\Models\MasterRecord::query()
                    ->forWorkspace(app(\App\Services\MasterDataService::class)->workspaceId())
                    ->ofType('supplier')
                    ->active()
                    ->find((int) $this->editOrderProductSupplierId, ['id', 'name', 'code', 'status']);
            }
        }

        $shipmentUrgencyOptions = $master->active('shipment_urgency');
        $orderDetailContext = app(OrderDetailViewService::class)->build($selected, $user, $shipmentUrgencyOptions);
        $orderRedoContext = $preloadedRedoContext
            ?? app(OrderRedoService::class)->context($selected, $user);
        $orderRedoForm = $this->redoFormState($selected);

        return [
            'selectedJob' => $selected,
            'selectedTask' => null,
            'taskStatuses' => $this->detailTab === 'overview' && $orderDetailSectionsReady['workflow'] ? $this->taskStatusOptions($master) : collect(),
            'users' => $this->detailTab === 'overview' && $orderDetailSectionsReady['workflow'] ? $this->userOptions($user) : collect(),
            'priorities' => $this->detailTab === 'overview' && $orderDetailSectionsReady['workflow'] ? $master->active('priority') : collect(),
            'shipmentUrgencyOptions' => $shipmentUrgencyOptions,
            'orderDetailContext' => $orderDetailContext,
            'orderRedoContext' => $orderRedoContext,
            'orderRedoForm' => $orderRedoForm,
            'overviewPhaseId' => $this->overviewPhaseId,
            'orderDetailSectionsReady' => $orderDetailSectionsReady,
            // Product/category options on Job Details are loaded remotely only
            // when an inline dropdown opens, avoiding full catalog payloads.
            'products' => collect(),
            'categories' => collect(),
            'availableDocuments' => $availableDocuments,
            'overviewTaskDocumentModalTask' => $overviewTaskDocumentModalTask,
            'overviewTaskAvailableDocuments' => $overviewTaskAvailableDocuments,
            'mentionUsers' => app(\App\Services\MentionService::class)->optionsForJob($selected, $user),
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
            'canViewFinance' => app(AccessControlService::class)->can($user, 'finance', 'view'),
            'jobProductSearchResults' => $jobProductSearchResults,
            'jobProductSearchSuppliers' => $jobProductSearchSuppliers,
            'jobProductResultTotal' => $jobProductResultTotal,
            'jobProductSelectedProduct' => $jobProductSelectedProduct,
            'jobProductSelectedSupplier' => $jobProductSelectedSupplier,
            'editOrderProductSearchResults' => $editOrderProductSearchResults,
            'editOrderProductSearchSuppliers' => $editOrderProductSearchSuppliers,
            'editOrderProductResultTotal' => $editOrderProductResultTotal,
            'editOrderProductSelectedProduct' => $editOrderProductSelectedProduct,
            'editOrderProductSelectedSupplier' => $editOrderProductSelectedSupplier,
        ];
    }

    private function jobsTableData(User $user): array
    {
        // The Orders list is intentionally its own lightweight render branch.
        // It does not hydrate filter catalogs, task collections, members or
        // inline-edit option lists that are not visible in the supplied
        // performance prototype.
        $jobs = app(OrderListQuery::class)->paginateLegacy(
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

    private function jobFilters(): array
    {
        return [
            'search' => $this->search,
            'phase' => $this->phase,
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
        $this->overviewTaskDocumentUpload = [];
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
