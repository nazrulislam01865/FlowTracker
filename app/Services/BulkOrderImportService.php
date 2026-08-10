<?php

namespace App\Services;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\MasterRecord;
use App\Models\User;
use App\Models\WorkflowTemplate;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class BulkOrderImportService
{
    private const ALIASES = [
        'ref' => ['referenceorderno', 'orderno', 'referenceorder'],
        'client_id' => ['clientid', 'clientcode'],
        'title' => ['ordertitle'],
        'received' => ['receiveddate'],
        'urgent' => ['urgentorderornot', 'urgent'],
        'description' => ['orderdescription', 'description'],
        'supplier_id' => ['supplierid', 'suppliercode', 'supplier'],
        'warehouse' => ['warehouse'],
        'workflow' => ['workflow'],
        'delivery' => ['requireddeliverydate', 'deliverydate'],
        'supplier_instruction' => ['supplierinstruction', 'scmail'],
        'source_id' => ['sourcerowid', 'sourceid'],
    ];

    public function uploadOptions(User $actor): array
    {
        abort_unless(app(AccessControlService::class)->can($actor, 'jobs', 'create'), 403);

        $clients = app(ClientService::class)->visibleQuery($actor)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $suppliers = MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType('supplier')
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return compact('clients', 'suppliers');
    }

    public function prepareUpload(UploadedFile $file, User $actor, ?string $displayFilename = null, ?string $sourceFingerprint = null): array
    {
        abort_unless(app(AccessControlService::class)->can($actor, 'jobs', 'create'), 403);
        if ($file->getSize() > 20 * 1024 * 1024) throw new RuntimeException('File exceeds the 20 MB limit.');

        $parsed = app(SpreadsheetRowReader::class)->read($file);
        if (count($parsed['rows']) > 10000) throw new RuntimeException('The file contains more than 10,000 data rows.');

        $token = (string) Str::uuid();
        $payload = [
            'user_id' => (int) $actor->id,
            'workspace_id' => app(SetupContext::class)->workspaceId(),
            'filename' => $displayFilename ?: $file->getClientOriginalName(),
            'fingerprint' => $sourceFingerprint ?: hash_file('sha256', $file->getRealPath()),
            'header_row' => $parsed['header_row'],
            'headers' => $parsed['headers'],
            'rows' => $parsed['rows'],
            'created_at' => now()->toIso8601String(),
        ];

        Storage::disk('local')->put($this->tempPath($actor, $token), json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return [
            'token' => $token,
            'filename' => $payload['filename'],
            'fingerprint' => $payload['fingerprint'],
            'header_row' => $payload['header_row'],
            'row_count' => count($payload['rows']),
        ];
    }

    public function validateToken(string $token, array $config, User $actor): array
    {
        $source = $this->loadToken($token, $actor);
        $config = $this->normalizeConfig($config, $actor);
        $rows = collect($source['rows'])->map(fn (array $row) => $this->mapRow($row))->values();

        $referenceCounts = $rows->pluck('ref')->filter()->countBy();
        $sourceIdCounts = $rows->pluck('source_id')->filter()->countBy();

        $clientMaps = $this->clientMaps($actor);
        $supplierMaps = $this->supplierMaps();
        $templates = $this->workflowTemplates();
        $existingByReference = $this->existingJobsByReferences($rows->pluck('ref')->filter()->unique()->all());
        $existingBySourceId = $this->existingJobsBySourceIds($rows->pluck('source_id')->filter()->unique()->all());

        $validated = $rows->map(function (array $row) use ($actor, $config, $referenceCounts, $sourceIdCounts, $clientMaps, $supplierMaps, $templates, $existingByReference, $existingBySourceId): array {
            $errors = [];
            $warnings = [];
            $action = 'create';

            $row['ref'] = trim((string) ($row['ref'] ?? ''));
            $row['title'] = trim((string) ($row['title'] ?? ''));
            $row['description'] = trim((string) ($row['description'] ?? ''));
            $row['warehouse'] = trim((string) ($row['warehouse'] ?? ''));
            $row['supplier_instruction'] = trim((string) ($row['supplier_instruction'] ?? ''));
            $row['source_id'] = trim((string) ($row['source_id'] ?? ''));
            $urgentRaw = trim((string) ($row['urgent'] ?? ''));
            if ($urgentRaw === '') {
                $errors[] = 'Urgent? is required';
                $row['urgent'] = 'No';
            } elseif (!$this->isAcceptedBoolean($urgentRaw)) {
                $errors[] = 'Urgent? must be Yes or No';
                $row['urgent'] = 'No';
            } else {
                $row['urgent'] = $this->normalizeBoolean($urgentRaw) ? 'Yes' : 'No';
            }

            if ($row['ref'] === '') $errors[] = 'Reference order is required';
            if ($row['title'] === '') $errors[] = 'Order title is required';
            if (mb_strlen($row['title']) > 255) $errors[] = 'Order title must be 255 characters or fewer';
            if ($row['description'] === '') $errors[] = 'Order description is required';
            if (mb_strlen($row['description']) > 10000) $errors[] = 'Order description is too long';
            if ($row['source_id'] !== '' && mb_strlen($row['source_id']) > 191) $errors[] = 'Source Row ID must be 191 characters or fewer';

            $row['received_normalized'] = $this->normalizeDate($row['received'] ?? null);
            if (!$row['received_normalized']) $errors[] = 'Invalid received date';
            $row['delivery_normalized'] = blank($row['delivery'] ?? null) ? null : $this->normalizeDate($row['delivery']);
            if (filled($row['delivery'] ?? null) && !$row['delivery_normalized']) {
                $errors[] = 'Invalid required delivery date';
            } elseif ($row['delivery_normalized'] && $row['received_normalized'] && $row['delivery_normalized'] < $row['received_normalized']) {
                $errors[] = 'Required delivery date cannot be earlier than received date';
            }

            $clientValue = trim((string) ($row['client_id'] ?? ''));
            $client = $clientValue !== '' ? $this->lookupClient($clientValue, $clientMaps) : ($config['default_client'] ?? null);
            if ($clientValue !== '' && !$client) $errors[] = 'Client ID does not match an active visible client';
            $row['client_resolved_id'] = $client?->id;
            $row['client_resolved_label'] = $client ? $client->code.' · '.$client->name : 'Unassigned';

            $supplierValue = trim((string) ($row['supplier_id'] ?? ''));
            $supplier = $supplierValue !== '' ? $this->lookupSupplier($supplierValue, $supplierMaps) : ($config['default_supplier'] ?? null);
            if ($supplierValue !== '' && !$supplier) $errors[] = 'Supplier ID does not match an active Supplier in Master Data';
            if (!$supplier) $warnings[] = 'Supplier ID will remain unassigned';
            $row['supplier_resolved_id'] = $supplier?->id;
            $row['supplier_resolved_label'] = $supplier ? $supplier->code.' · '.$supplier->name : 'Unassigned';

            // Bulk imports follow the same client-driven Workflow Setup used by
            // the normal New Order form. A spreadsheet can therefore contain many
            // clients in the same file and each row independently gets that
            // client's preferred Workflow. The legacy Workflow column is treated
            // as informational only so an old IID/NEP value cannot override a
            // client's current configured default.
            $resolvedWorkflow = $client ? $this->resolvePreferredWorkflow($templates, (int) $client->id) : null;
            if (!$client) {
                if ($clientValue === '' && !$config['default_client']) {
                    $errors[] = 'Client ID is required so FlowTrack can select the client workflow';
                }
                $row['workflow_resolved_id'] = null;
                $row['workflow_phase_id'] = null;
                $row['workflow_resolved_label'] = 'Waiting for client';
            } elseif (!$resolvedWorkflow) {
                $errors[] = 'No active workflow is configured for this client';
                $row['workflow_resolved_id'] = null;
                $row['workflow_phase_id'] = null;
                $row['workflow_resolved_label'] = 'Not configured';
            } else {
                $row['workflow_resolved_id'] = $resolvedWorkflow['workflow']->id;
                $row['workflow_phase_id'] = $resolvedWorkflow['phase']->id;
                $row['workflow_resolved_label'] = $resolvedWorkflow['workflow']->name;
            }

            if ($row['ref'] !== '' && ($referenceCounts[$row['ref']] ?? 0) > 1) $warnings[] = 'Duplicate reference in this file';
            if ($row['source_id'] !== '' && ($sourceIdCounts[$row['source_id']] ?? 0) > 1) $errors[] = 'Duplicate Source Row ID in this file';

            $sourceExisting = $row['source_id'] !== '' ? ($existingBySourceId[$row['source_id']] ?? null) : null;
            $referenceExisting = $row['ref'] !== '' ? ($existingByReference[$row['ref']] ?? collect()) : collect();

            if ($sourceExisting) {
                if ($config['duplicate_policy'] === 'update' && $this->canUpdateExisting($actor, $sourceExisting)) {
                    $action = 'update';
                    $row['existing_job_id'] = $sourceExisting->id;
                    $warnings[] = 'Source Row ID already exists; the matching order will be updated';
                } else {
                    $action = 'skip';
                    $row['existing_job_id'] = $sourceExisting->id;
                    $warnings[] = 'Source Row ID was already imported; this row will be skipped';
                }
            } elseif ($referenceExisting->isNotEmpty()) {
                if ($config['duplicate_policy'] === 'skip') {
                    $action = 'skip';
                    $row['existing_job_id'] = $referenceExisting->first()->id;
                    $warnings[] = 'Reference already exists; this row will be skipped';
                } elseif ($config['duplicate_policy'] === 'update') {
                    if ($referenceExisting->count() > 1) {
                        $errors[] = 'Multiple existing orders use this reference; update cannot choose one safely';
                    } elseif (!$this->canUpdateExisting($actor, $referenceExisting->first())) {
                        $errors[] = 'You do not have permission to update the existing order with this reference';
                    } else {
                        $action = 'update';
                        $row['existing_job_id'] = $referenceExisting->first()->id;
                        $warnings[] = 'Reference already exists; the matching order will be updated and its workflow snapshot will be preserved';
                    }
                } else {
                    $warnings[] = 'Reference already exists; a separate order will be created';
                }
            }

            $row['priority_resolved'] = $row['urgent'] === 'Yes' ? 'Critical' : 'Medium';
            $row['import_profile_resolved'] = $resolvedWorkflow ? 'CLIENT_AUTO' : null;
            $row['action'] = $action;
            $row['errors'] = array_values(array_unique($errors));
            $row['warnings'] = array_values(array_unique($warnings));
            $row['status'] = $row['errors'] !== [] ? 'error' : ($row['warnings'] !== [] ? 'warning' : 'ready');

            return $row;
        })->all();

        $counts = [
            'total' => count($validated),
            'ready' => collect($validated)->where('status', 'ready')->count(),
            'warnings' => collect($validated)->where('status', 'warning')->count(),
            'errors' => collect($validated)->where('status', 'error')->count(),
            'importable' => collect($validated)->where('status', '!=', 'error')->where('action', '!=', 'skip')->count(),
            'skippable' => collect($validated)->where('status', '!=', 'error')->where('action', 'skip')->count(),
        ];

        return [
            'token' => $token,
            'filename' => $source['filename'],
            'fingerprint' => $source['fingerprint'],
            'header_row' => $source['header_row'],
            'workflow_label' => 'Client-based workflow',
            'counts' => $counts,
            'rows' => $validated,
        ];
    }

    public function import(string $token, array $config, User $actor): array
    {
        abort_unless(app(AccessControlService::class)->can($actor, 'jobs', 'create'), 403);
        $source = $this->loadToken($token, $actor);
        $validated = $this->validateToken($token, $config, $actor);
        $config = $this->normalizeConfig($config, $actor);

        $importId = DB::table('bulk_order_imports')->insertGetId([
            'workspace_id' => app(SetupContext::class)->workspaceId(),
            'import_number' => 'PENDING-'.Str::uuid(),
            'user_id' => $actor->id,
            'profile' => 'CLIENT_AUTO',
            'default_client_id' => $config['default_client']?->id,
            'default_supplier_id' => $config['default_supplier']?->id,
            'duplicate_policy' => $config['duplicate_policy'],
            'original_filename' => $source['filename'],
            'file_fingerprint' => $source['fingerprint'],
            'total_rows' => count($validated['rows']),
            'created_count' => 0,
            'updated_count' => 0,
            'skipped_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $importNumber = 'IMP-'.app(WorkspaceSettingsService::class)->localNow()->format('Y').'-'.str_pad((string) $importId, 4, '0', STR_PAD_LEFT);
        DB::table('bulk_order_imports')->where('id', $importId)->update(['import_number' => $importNumber]);

        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0];
        $results = [];

        foreach ($validated['rows'] as $row) {
            $status = 'failed';
            $message = '';
            $jobId = null;

            if ($row['errors'] !== []) {
                $counts['failed']++;
                $message = implode(' · ', $row['errors']);
            } elseif ($row['action'] === 'skip') {
                $status = 'skipped';
                $counts['skipped']++;
                $jobId = $row['existing_job_id'] ?? null;
                $message = implode(' · ', $row['warnings']) ?: 'Skipped by duplicate policy';
            } else {
                try {
                    if ($row['action'] === 'update') {
                        $job = $this->updateExisting($row, $actor, $importNumber);
                        $status = 'updated';
                        $counts['updated']++;
                    } else {
                        $job = $this->createOrder($row, $actor, $importNumber);
                        $status = 'created';
                        $counts['created']++;
                    }
                    $jobId = $job->id;
                    $message = $job->displayOrderNumber();
                } catch (Throwable $exception) {
                    report($exception);
                    $counts['failed']++;
                    $message = trim($exception->getMessage()) ?: 'The order could not be imported.';
                }
            }

            DB::table('bulk_order_import_rows')->insert([
                'bulk_order_import_id' => $importId,
                'source_row_number' => (int) ($row['row'] ?? 0),
                'source_row_id' => blank($row['source_id'] ?? null) ? null : $row['source_id'],
                'reference_order_no' => blank($row['ref'] ?? null) ? null : $row['ref'],
                'flow_job_id' => $jobId,
                'status' => $status,
                'message' => Str::limit($message, 1000, ''),
                'payload' => json_encode($this->auditPayload($row), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $results[] = [
                'row' => $row['row'],
                'reference' => $row['ref'],
                'status' => $status,
                'message' => $message,
                'job_id' => $jobId,
            ];
        }

        DB::table('bulk_order_imports')->where('id', $importId)->update([
            'created_count' => $counts['created'],
            'updated_count' => $counts['updated'],
            'skipped_count' => $counts['skipped'],
            'failed_count' => $counts['failed'],
            'status' => $counts['failed'] > 0 ? 'completed_with_issues' : 'completed',
            'updated_at' => now(),
        ]);

        return [
            'import_id' => $importId,
            'import_number' => $importNumber,
            'counts' => $counts,
            'results' => $results,
            'fingerprint' => $source['fingerprint'],
        ];
    }

    private function createOrder(array $row, User $actor, string $importNumber): FlowJob
    {
        return app(JobService::class)->create([
            'order_number' => $row['ref'],
            'client_id' => $row['client_resolved_id'],
            'workflow_id' => $row['workflow_resolved_id'],
            'workflow_phase_id' => $row['workflow_phase_id'],
            'owner_id' => $actor->id,
            'coordinator_id' => $actor->id,
            'title' => $row['title'],
            'product' => null,
            'category' => null,
            'quantity' => 0,
            'items' => [],
            'delivery_date' => $row['delivery_normalized'],
            'priority' => $row['priority_resolved'],
            'description' => $row['description'],
            'received_date' => $row['received_normalized'],
            'supplier_id' => $row['supplier_resolved_id'],
            'warehouse' => blank($row['warehouse']) ? null : $row['warehouse'],
            'supplier_instruction' => blank($row['supplier_instruction']) ? null : $row['supplier_instruction'],
            'source_row_id' => blank($row['source_id']) ? null : $row['source_id'],
            'import_profile' => $row['import_profile_resolved'] ?? null,
            'bulk_import_id' => $importNumber,
            'draft' => false,
        ], $actor);
    }

    private function updateExisting(array $row, User $actor, string $importNumber): FlowJob
    {
        $job = FlowJob::query()->findOrFail((int) $row['existing_job_id']);
        abort_unless($this->canUpdateExisting($actor, $job), 403);

        DB::transaction(function () use ($job, $row, $actor, $importNumber): void {
            $job->update([
                'order_number' => $row['ref'],
                'client_id' => $row['client_resolved_id'],
                'title' => $row['title'],
                'priority' => $row['priority_resolved'],
                'delivery_date' => $row['delivery_normalized'],
                'description' => app(RichTextService::class)->normalize($row['description'], 10000, 'description'),
                'received_date' => $row['received_normalized'],
                'supplier_id' => $row['supplier_resolved_id'],
                'warehouse' => blank($row['warehouse']) ? null : $row['warehouse'],
                'supplier_instruction' => blank($row['supplier_instruction']) ? null : $row['supplier_instruction'],
                'source_row_id' => blank($row['source_id']) ? $job->source_row_id : $row['source_id'],
                'import_profile' => $row['import_profile_resolved'] ?? null,
                'bulk_import_id' => $importNumber,
            ]);

            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.bulk_import_updated',
                'description' => 'Order updated by bulk import '.$importNumber,
            ]);
        });

        return $job->refresh();
    }

    private function normalizeConfig(array $config, User $actor): array
    {
        $policy = strtolower(trim((string) ($config['duplicate_policy'] ?? 'skip')));
        if (!in_array($policy, ['skip', 'update', 'separate'], true)) throw new RuntimeException('Invalid duplicate reference policy.');

        $defaultClient = null;
        if (filled($config['default_client_id'] ?? null)) {
            $defaultClient = app(ClientService::class)->visibleQuery($actor)
                ->where('is_active', true)
                ->whereKey((int) $config['default_client_id'])
                ->first();
            if (!$defaultClient) throw new RuntimeException('The selected default client is not available.');
        }

        $defaultSupplier = null;
        if (filled($config['default_supplier_id'] ?? null)) {
            $defaultSupplier = MasterRecord::query()
                ->forWorkspace(app(SetupContext::class)->workspaceId())
                ->ofType('supplier')
                ->active()
                ->whereKey((int) $config['default_supplier_id'])
                ->first();
            if (!$defaultSupplier) throw new RuntimeException('The selected default supplier is not available.');
        }

        return [
            'duplicate_policy' => $policy,
            'default_client' => $defaultClient,
            'default_supplier' => $defaultSupplier,
        ];
    }

    private function mapRow(array $source): array
    {
        $normalized = [];
        foreach ($source as $key => $value) {
            if ($key === '__source_row') continue;
            $normalized[$this->normalizeKey((string) $key)] = $value;
        }

        $row = ['row' => (int) ($source['__source_row'] ?? 0)];
        foreach (self::ALIASES as $target => $aliases) {
            $row[$target] = '';
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $normalized)) {
                    $row[$target] = $normalized[$alias];
                    break;
                }
            }
        }
        return $row;
    }

    private function normalizeKey(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', trim($value)) ?? '');
    }

    private function isAcceptedBoolean(mixed $value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['yes', 'y', 'true', '1', 'no', 'n', 'false', '0'], true);
    }

    private function normalizeBoolean(mixed $value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['yes', 'y', 'true', '1'], true);
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) return $value->format('Y-m-d');
        $text = trim((string) $value);
        if ($text === '') return null;

        if (is_numeric($text)) {
            $serial = (float) $text;
            if ($serial >= 1 && $serial <= 2958465) {
                $base = new DateTimeImmutable('1899-12-30', new DateTimeZone('UTC'));
                return $base->modify('+'.(int) floor($serial).' days')->format('Y-m-d');
            }
        }

        foreach (['Y-m-d', 'Y/m/d', 'm/d/Y', 'n/j/Y', 'd/m/Y', 'j/n/Y', 'm-d-Y', 'n-j-Y'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $text);
            $errors = DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) && $date->format($format) === $text) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /** @return array{by_id:array<int,Client>,by_code:array<string,Client>,by_name:array<string,Client>} */
    private function clientMaps(User $actor): array
    {
        $clients = app(ClientService::class)->visibleQuery($actor)->where('is_active', true)->get(['id', 'code', 'name']);
        return [
            'by_id' => $clients->keyBy('id')->all(),
            'by_code' => $clients->keyBy(fn ($client) => strtoupper(trim((string) $client->code)))->all(),
            'by_name' => $clients->keyBy(fn ($client) => mb_strtolower(trim((string) $client->name)))->all(),
        ];
    }

    private function lookupClient(string $value, array $maps): ?Client
    {
        if (ctype_digit($value) && isset($maps['by_id'][(int) $value])) return $maps['by_id'][(int) $value];
        $code = strtoupper($value);
        if (isset($maps['by_code'][$code])) return $maps['by_code'][$code];
        return $maps['by_name'][mb_strtolower($value)] ?? null;
    }

    /** @return array{by_id:array<int,MasterRecord>,by_code:array<string,MasterRecord>,by_name:array<string,MasterRecord>} */
    private function supplierMaps(): array
    {
        $suppliers = MasterRecord::query()
            ->forWorkspace(app(SetupContext::class)->workspaceId())
            ->ofType('supplier')
            ->active()
            ->get(['id', 'code', 'name']);
        return [
            'by_id' => $suppliers->keyBy('id')->all(),
            'by_code' => $suppliers->keyBy(fn ($supplier) => strtoupper(trim((string) $supplier->code)))->all(),
            'by_name' => $suppliers->keyBy(fn ($supplier) => mb_strtolower(trim((string) $supplier->name)))->all(),
        ];
    }

    private function lookupSupplier(string $value, array $maps): ?MasterRecord
    {
        if (ctype_digit($value) && isset($maps['by_id'][(int) $value])) return $maps['by_id'][(int) $value];
        $code = strtoupper($value);
        if (isset($maps['by_code'][$code])) return $maps['by_code'][$code];
        $prefixed = str_starts_with($code, 'SUP-') ? $code : 'SUP-'.$code;
        if (isset($maps['by_code'][$prefixed])) return $maps['by_code'][$prefixed];
        return $maps['by_name'][mb_strtolower($value)] ?? null;
    }

    private function workflowTemplates(): Collection
    {
        return WorkflowTemplate::query()
            ->where('workspace_id', app(SetupContext::class)->workspaceId())
            ->where('is_active', true)
            ->with([
                'clients:id',
                'phases' => fn ($query) => $query->where('is_active', true)->orderBy('sequence'),
            ])
            ->get();
    }

    private function resolvePreferredWorkflow(Collection $templates, int $clientId): ?array
    {
        // Keep this priority identical to Jobs\Index::preferredCreateOrderWorkflowId():
        // client-specific Inquiry workflow, client-specific Order workflow, then
        // the normal all-client Order workflow; defaults win within each tier.
        $workflow = $templates
            ->filter(fn (WorkflowTemplate $workflow) => $this->workflowAvailableForClient($workflow, $clientId))
            ->sortBy(fn (WorkflowTemplate $workflow) => [
                $workflow->client_availability === 'specific' && $workflow->applies_to === 'inquiries' ? 0
                    : ($workflow->client_availability === 'specific' && $workflow->applies_to === 'orders' ? 1 : 2),
                $workflow->is_default ? 0 : 1,
                mb_strtolower($workflow->name),
            ])
            ->first();

        if (!$workflow) return null;

        $phase = $workflow->phases->first(fn ($phase) => $phase->is_active && $phase->allow_job_start);
        return $phase ? compact('workflow', 'phase') : null;
    }

    private function workflowAvailableForClient(WorkflowTemplate $workflow, ?int $clientId): bool
    {
        if ($workflow->applies_to === 'orders') {
            if ($workflow->client_availability === 'all') return true;
            return $clientId && $workflow->client_availability === 'specific' && $workflow->clients->contains('id', $clientId);
        }

        return $workflow->applies_to === 'inquiries'
            && $clientId
            && $workflow->client_availability === 'specific'
            && $workflow->clients->contains('id', $clientId);
    }

    /** @return array<string,Collection<int,FlowJob>> */
    private function existingJobsByReferences(array $references): array
    {
        $map = [];
        foreach (array_chunk($references, 400) as $chunk) {
            FlowJob::query()->whereIn('order_number', $chunk)->orderByDesc('id')->get()
                ->groupBy('order_number')
                ->each(function (Collection $jobs, string $reference) use (&$map): void { $map[$reference] = $jobs; });
        }
        return $map;
    }

    /** @return array<string,FlowJob> */
    private function existingJobsBySourceIds(array $sourceIds): array
    {
        $map = [];
        foreach (array_chunk($sourceIds, 400) as $chunk) {
            FlowJob::withTrashed()->whereIn('source_row_id', $chunk)->get()->each(function (FlowJob $job) use (&$map): void {
                if ($job->source_row_id !== null) $map[$job->source_row_id] = $job;
            });
        }
        return $map;
    }

    private function canUpdateExisting(User $actor, FlowJob $job): bool
    {
        if ($job->trashed()) return false;
        return app(AccessControlService::class)->canEditJob($actor, $job);
    }

    private function loadToken(string $token, User $actor): array
    {
        if (!Str::isUuid($token)) throw new RuntimeException('The import session is invalid. Upload the file again.');
        $path = $this->tempPath($actor, $token);
        if (!Storage::disk('local')->exists($path)) throw new RuntimeException('The import session expired. Upload the file again.');
        $source = json_decode(Storage::disk('local')->get($path), true);
        if (!is_array($source) || (int) ($source['user_id'] ?? 0) !== (int) $actor->id || (int) ($source['workspace_id'] ?? 0) !== app(SetupContext::class)->workspaceId()) {
            throw new RuntimeException('The import session is invalid. Upload the file again.');
        }
        return $source;
    }

    private function tempPath(User $actor, string $token): string
    {
        return 'bulk-order-imports/tmp/'.$actor->id.'/'.$token.'.json';
    }

    private function auditPayload(array $row): array
    {
        return collect($row)->except([
            'errors', 'warnings', 'status', 'existing_job_id',
            'client_resolved_label', 'supplier_resolved_label', 'workflow_resolved_label',
        ])->all();
    }
}
