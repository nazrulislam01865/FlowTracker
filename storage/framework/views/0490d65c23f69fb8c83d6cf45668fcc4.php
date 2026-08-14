<?php $__env->startSection('content'); ?>
<div
    id="ft-bulk-import-page"
    data-validate-url="<?php echo e(route('orders.bulk-import.validate')); ?>"
    data-revalidate-url="<?php echo e(route('orders.bulk-import.revalidate')); ?>"
    data-import-url="<?php echo e(route('orders.bulk-import.import')); ?>"
    data-template-url="<?php echo e(route('orders.bulk-import.template')); ?>"
    data-orders-url="<?php echo e(route('jobs.index')); ?>"
>
    <header class="ftbi-page-head">
        <div class="ftbi-heading-copy">
            <a class="ftbi-breadcrumb" href="<?php echo e(route('jobs.index')); ?>" wire:navigate>
                <span aria-hidden="true">‹</span> Orders
            </a>
            <h1>Import orders</h1>
            <p>Create many orders safely from an Excel or CSV file.</p>
        </div>
        <div class="ftbi-head-actions">
            <a class="btn" href="<?php echo e(route('jobs.index')); ?>" wire:navigate>Back to orders</a>
            <a class="btn ftbi-template-btn" id="downloadTemplate" href="<?php echo e(route('orders.bulk-import.template')); ?>">
                <span aria-hidden="true">⇩</span> Download template
            </a>
        </div>
    </header>

    <nav class="steps" aria-label="Bulk order import progress">
        <div class="step active" data-step="1">
            <span class="n">1</span>
            <div><strong>Upload file</strong><span>Select Excel or CSV</span></div>
        </div>
        <div class="step" data-step="2">
            <span class="n">2</span>
            <div><strong>Configure</strong><span>Apply import defaults</span></div>
        </div>
        <div class="step" data-step="3">
            <span class="n">3</span>
            <div><strong>Review</strong><span>Check validation</span></div>
        </div>
        <div class="step" data-step="4">
            <span class="n">4</span>
            <div><strong>Import</strong><span>Create orders</span></div>
        </div>
    </nav>

    <section class="card upload" id="uploadCard">
        <div class="ftbi-upload-grid">
            <div class="ftbi-file-panel">
                <div class="ftbi-section-heading">
                    <span class="ftbi-section-number">1</span>
                    <div>
                        <h2>Select your order file</h2>
                        <p>Use the FlowTrack template for the cleanest import.</p>
                    </div>
                </div>

                <div class="drop" id="drop" tabindex="0" role="button" aria-label="Choose or drop an order file">
                    <span class="ftbi-upload-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14.5v3A2.5 2.5 0 0 0 7.5 20h9a2.5 2.5 0 0 0 2.5-2.5v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <h3>Drop your order file here</h3>
                    <p class="ftbi-file-limits">.xlsx, .xls or .csv <span>•</span> up to 10,000 rows <span>•</span> maximum 20 MB</p>
                    <label class="btn primary ftbi-choose-btn" for="bulkOrderFile">Choose file</label>
                    <input class="fileInput" id="bulkOrderFile" type="file" accept=".xlsx,.xls,.csv">
                    <div class="ftbi-safe-note"><span aria-hidden="true">✓</span> Nothing is created until you confirm the import.</div>
                </div>

                <div class="ftbi-template-note">
                    <div class="ftbi-note-icon" aria-hidden="true">i</div>
                    <div>
                        <strong>Need the correct column format?</strong>
                        <span>Download the supplied template, add your orders, then upload it here.</span>
                    </div>
                    <a href="<?php echo e(route('orders.bulk-import.template')); ?>">Download template</a>
                </div>
            </div>

            <aside class="ftbi-config-panel" aria-label="Import setup">
                <div class="ftbi-section-heading compact">
                    <span class="ftbi-section-number">2</span>
                    <div>
                        <h2>Import setup</h2>
                        <p>Each spreadsheet row must resolve to an active client.</p>
                    </div>
                </div>

                <div class="ftbi-config-fields">
                    <div class="ftbi-client-workflow-note">
                        <span class="ftbi-auto-icon" aria-hidden="true">✓</span>
                        <div><strong>Client-based workflow</strong><span>A client-specific Order workflow is applied automatically. If that client has no specific Order workflow, select an available Order workflow during Review.</span></div>
                    </div>
                    <div class="field">
                        <label for="client">Fallback Client ID <span class="ftbi-optional">For blank Client IDs</span></label>
                        <select id="client">
                            <option value="">No fallback client</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($client->id); ?>"><?php echo e($client->code); ?> · <?php echo e($client->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <div class="hint">Used only when a row's Client ID is blank. Every imported order must have a client.</div>
                    </div>
                    <div class="field">
                        <label for="supplier">Fallback Supplier ID <span class="ftbi-optional">Optional</span></label>
                        <select id="supplier">
                            <option value="">Keep blank values unassigned</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->code); ?> · <?php echo e($supplier->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <div class="hint">Only used when that row's Supplier ID is blank.</div>
                    </div>
                </div>

                <details class="ftbi-source-help">
                    <summary>What is Source Row ID?</summary>
                    <p>It is an optional stable identifier from the source system. FlowTrack uses it to prevent the same source row being created twice during re-import and to trace import results.</p>
                </details>

                <details class="ftbi-test-tools">
                    <summary>Test the importer with sample data</summary>
                    <div class="ftbi-test-actions">
                        <button class="btn" type="button" id="demo">Use sample data</button>
                        <button class="btn" type="button" id="errorDemo">Preview errors</button>
                    </div>
                </details>
            </aside>
        </div>
    </section>

    <section class="card content-card hidden" id="reviewCard">
        <div class="toolbar">
            <div class="ftbi-file-summary">
                <span class="ftbi-file-icon" aria-hidden="true">XLS</span>
                <div>
                    <h2 id="fileName">order-import.xlsx</h2>
                    <div class="hint" id="fileMeta">0 rows · source fields normalized · workflow resolved automatically</div>
                </div>
            </div>
            <div class="ftbi-review-actions">
                <button class="btn" type="button" id="changeFile">Change file</button>
                <div class="pills" aria-label="Validation summary">
                    <span class="pill" id="totalPill">0 rows</span>
                    <span class="pill ok" id="validPill">0 ready</span>
                    <span class="pill warn" id="warnPill">0 warnings</span>
                    <span class="pill err" id="errorPill">0 errors</span>
                </div>
            </div>
        </div>

        <div class="ftbi-review-config">
            <div class="ftbi-review-config-head">
                <div>
                    <strong>Import configuration</strong>
                    <span>Client ID on each row takes priority. Client-specific Order workflows are automatic; rows without one will ask for a workflow below.</span>
                </div>
                <div class="ftbi-workflow-badge" id="configBanner">Workflow: <b id="workflowText">Resolving client workflows</b></div>
            </div>

            <div class="meta">
                <div class="field">
                    <label for="reviewClient">Fallback Client ID <span class="ftbi-optional">For blank Client IDs</span></label>
                    <select id="reviewClient">
                        <option value="">No fallback client</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($client->id); ?>"><?php echo e($client->code); ?> · <?php echo e($client->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="reviewSupplier">Fallback Supplier ID <span class="ftbi-optional">Optional</span></label>
                    <select id="reviewSupplier">
                        <option value="">Keep blank values unassigned</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->code); ?> · <?php echo e($supplier->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="duplicate">If reference already exists <span class="ftbi-required">*</span></label>
                    <select id="duplicate">
                        <option value="skip">Skip existing order</option>
                        <option value="update">Update matching order</option>
                        <option value="separate">Create a separate order</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ftbi-review-heading">
            <div>
                <h3>Review rows</h3>
                <p id="reviewStatusText">Check the validation result before importing.</p>
            </div>
            <button class="btn" type="button" id="exportErrors">⇩ Download issue rows</button>
        </div>

        <div class="tablewrap">
            <table>
                <thead><tr><th>Row</th><th>Reference order</th><th>Client ID</th><th>Received</th><th>Urgent</th><th>Order description</th><th>Supplier / warehouse</th><th>Workflow</th><th>Validation</th></tr></thead>
                <tbody id="rows"></tbody>
            </table>
        </div>

        <div class="footerbar">
            <div class="checks"><span>✓ Dates normalized</span><span>✓ Text safely preserved</span><span>✓ No master data created silently</span></div>
            <button class="btn primary ftbi-import-btn" type="button" id="importBtn">Import ready orders</button>
        </div>
    </section>

    <section class="card success" id="success">
        <div class="successIcon">✓</div>
        <h2>Order import completed</h2>
        <p class="sub">Valid orders were created. Rows needing attention were not changed.</p>
        <div class="summary">
            <div class="sum"><span>Created</span><b id="created">0</b></div>
            <div class="sum"><span>Updated</span><b id="updated">0</b></div>
            <div class="sum"><span>Skipped</span><b id="skipped">0</b></div>
            <div class="sum"><span>Failed</span><b id="failed">0</b></div>
        </div>
        <div class="ftbi-import-id">Import ID <b id="importId">—</b> <span>•</span> Audit log and source fingerprint saved.</div>
        <div class="ftbi-success-actions">
            <button class="btn" type="button" id="downloadResults">Download results</button>
            <a class="btn primary" id="viewImportedOrders" href="<?php echo e(route('jobs.index')); ?>">View imported orders</a>
        </div>
    </section>

    <div class="loading" id="loading" role="status" aria-live="polite">
        <div class="loaderCard">
            <div class="ftbi-loader-top">
                <span class="ftbi-loader-mark" aria-hidden="true"></span>
                <div>
                    <b id="loadTitle">Validating rows…</b>
                    <div class="sub" id="loadText">Checking references, dates, clients and workflows. No orders have been created yet.</div>
                </div>
            </div>
            <div class="bar"><i id="progress"></i></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="/js/flowtrack-bulk-order-import.js?v=20260814-client-workflow-1"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/pages/bulk-order-import.blade.php ENDPATH**/ ?>