<?php

namespace App\Services\Orders;

use App\DTOs\Email\EmailMessage;
use App\Models\Document;
use App\Models\FlowJob;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\BrandingService;
use App\Services\CompanyProfileService;
use App\Services\Email\EmailService;
use App\Services\Email\ModuleEmailControlService;
use App\Services\SecureDocumentStorage;
use App\Services\SetupContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Internal Order workflow email handoffs.
 *
 * The Order module only composes business context. Actual delivery stays behind
 * the central EmailService, so e2a/SMTP/SES/provider changes never leak into the
 * workflow implementation.
 */
final class OrderWorkflowEmailService
{
    private const PURCHASE_ORDER_HANDOFF = 'NEW_SEND_PO_ARTWORK';
    private const ARTWORK_HANDOFF = 'ART_SEND_ORDER_TEAM';
    private const DELIVERY_ATTEMPTS = 3;

    /** @var array<string,string> */
    private const SOURCE_TASK_KEYS = [
        self::PURCHASE_ORDER_HANDOFF => 'NEW_UPLOAD_PO',
        self::ARTWORK_HANDOFF => 'ART_PREPARE_UPLOAD',
    ];

    /** @var array<string,string> */
    private const SOURCE_TASK_TITLES = [
        'NEW_UPLOAD_PO' => 'upload purchase order',
        'ART_PREPARE_UPLOAD' => 'prepare & upload artwork',
        'ART_CLIENT_ERP_DECISION' => 'client erp / approval',
    ];

    public function __construct(
        private readonly EmailService $email,
        private readonly ModuleEmailControlService $emailControl,
        private readonly SecureDocumentStorage $storage,
        private readonly BrandingService $branding,
        private readonly CompanyProfileService $companyProfile,
    ) {}

    /**
     * Lightweight data used by the Order Details/List confirmation modal.
     * Missing recipients/files are returned as empty values so the modal can
     * explain what must be fixed before Send is pressed.
     *
     * @return array<string,mixed>
     */
    public function preview(Task $handoffTask, ?User $actor = null): array
    {
        $key = $this->automationKey($handoffTask);
        if (! in_array($key, [self::PURCHASE_ORDER_HANDOFF, self::ARTWORK_HANDOFF], true)) {
            return [];
        }

        $job = $handoffTask->job ?: FlowJob::query()->find($handoffTask->flow_job_id);
        if (! $job) return [];

        $job->loadMissing(['client', 'owner', 'coordinator', 'items']);
        $actor ??= auth()->user() instanceof User ? auth()->user() : ($job->owner ?: $job->coordinator);
        $emailServiceEnabled = $this->emailControl->orderEnabled();

        $recipients = $this->recipients($job, $key);
        $documents = $this->sourceDocuments($job, $key);
        $document = $documents->last();
        $subject = $this->subject($job, $key);
        $brand = $this->companyBrand();
        $viewData = ($document && $actor)
            ? $this->viewData($job, $key, $document, $documents, $actor, $brand)
            : [];
        $previewHtml = $viewData !== []
            ? view('emails.orders.workflow-handoff', $viewData)->render()
            : '';

        return [
            'key' => $key,
            'team' => $this->teamLabel($key),
            'recipients' => $recipients->map(fn (User $user) => [
                'id' => (int) $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
            ])->values()->all(),
            'recipient_count' => $recipients->count(),
            'recipient_source' => $this->recipientSourceLabel($key, $job),
            'empty_recipient_message' => $recipients->isEmpty()
                ? ($key === self::ARTWORK_HANDOFF
                    ? $this->missingOrderTeamRecipientMessage($job)
                    : 'No active Artwork phase assignee with a valid email address was found for this Order.')
                : '',
            'business_unit' => $key === self::ARTWORK_HANDOFF ? $this->orderBusinessUnit($job) : null,
            'subject' => $subject,
            'document_id' => $document?->id,
            'document_name' => $document?->name,
            'document_version' => $document?->version,
            'document_size' => $document?->size,
            'documents' => $documents->map(fn (Document $item) => [
                'id' => (int) $item->id,
                'name' => (string) $item->name,
                'version' => max(1, (int) $item->version),
                'size' => (int) $item->size,
            ])->values()->all(),
            'document_count' => $documents->count(),
            'from_name' => $this->senderName($brand),
            'from_address' => $this->senderAddress(),
            'reply_to' => $actor && filter_var((string) $actor->email, FILTER_VALIDATE_EMAIL) ? (string) $actor->email : '',
            'email_service_enabled' => $emailServiceEnabled,
            'delivery' => $emailServiceEnabled ? $this->deliveryLabel() : 'Order email service disabled',
            'html' => $previewHtml,
        ];
    }

    /**
     * Send the file-backed handoff synchronously. The workflow task must only be
     * completed after the configured provider has durably accepted the email.
     */
    public function send(Task $handoffTask, User $actor): string
    {
        $key = $this->automationKey($handoffTask);
        abort_unless(in_array($key, [self::PURCHASE_ORDER_HANDOFF, self::ARTWORK_HANDOFF], true), 422);

        $job = FlowJob::query()
            ->with(['client', 'owner', 'coordinator', 'items'])
            ->findOrFail($handoffTask->flow_job_id);

        if (! $this->emailControl->orderEnabled()) {
            $trackingId = 'disabled-'.Str::uuid();
            $attachmentLabel = $key === self::PURCHASE_ORDER_HANDOFF ? 'Purchase Order' : 'Artwork';

            $job->activities()->create([
                'user_id' => $actor->id,
                'event' => 'job.workflow_email_skipped',
                'description' => $attachmentLabel.' email handoff was skipped because the Order email service is disabled by an administrator.',
                'meta' => [
                    'task_id' => (int) $handoffTask->id,
                    'tracking_id' => $trackingId,
                    'email_service_disabled' => true,
                    'module' => ModuleEmailControlService::ORDER,
                ],
            ]);

            Log::info('flowtrack.order_workflow_email.skipped', [
                'flow_job_id' => (int) $job->id,
                'task_id' => (int) $handoffTask->id,
                'handoff_key' => $key,
                'reason' => 'order_email_service_disabled',
            ]);

            return $trackingId;
        }

        $recipients = $this->recipients($job, $key);
        if ($recipients->isEmpty()) {
            $message = $key === self::ARTWORK_HANDOFF
                ? $this->missingOrderTeamRecipientMessage($job)
                : 'No active Artwork phase assignee with a valid email address could be resolved for this Order. Assign the Artwork phase tasks to the Artwork team members before sending.';

            throw ValidationException::withMessages([
                'orderWorkflowActionEmail' => $message,
            ]);
        }

        $documents = $this->sourceDocuments($job, $key);
        $document = $documents->last();
        if ($documents->isEmpty() || ! $document) {
            $label = $key === self::PURCHASE_ORDER_HANDOFF ? 'Purchase Order' : 'Artwork';
            throw ValidationException::withMessages([
                'orderWorkflowActionEmail' => 'No uploaded '.$label.' file was found. Upload the file in the previous workflow task before sending this email.',
            ]);
        }

        $attachments = $documents->map(function (Document $item) {
            $located = $this->storage->locate((string) $item->path);
            if (! $located) {
                throw ValidationException::withMessages([
                    'orderWorkflowActionEmail' => 'An attachment record exists, but '.$item->name.' cannot be found. Re-upload the artwork set before sending.',
                ]);
            }

            return EmailMessage::storageAttachment(
                (string) $located['disk'],
                (string) $located['path'],
                (string) $item->name,
                filled($item->mime_type) ? (string) $item->mime_type : null,
            );
        })->values()->all();

        $brand = $this->companyBrand();
        $orderNumber = $job->displayOrderNumber();
        $team = $this->teamLabel($key);
        $subject = $this->subject($job, $key);
        $replyTo = filter_var((string) $actor->email, FILTER_VALIDATE_EMAIL) ? [(string) $actor->email] : [];
        $viewData = $this->viewData($job, $key, $document, $documents, $actor, $brand);

        $message = new EmailMessage(
            to: $recipients->pluck('email')->all(),
            subject: $subject,
            view: 'emails.orders.workflow-handoff',
            viewData: $viewData,
            replyTo: $replyTo,
            attachments: $attachments,
            context: [
                'type' => $key === self::PURCHASE_ORDER_HANDOFF ? 'order_purchase_order_handoff' : 'order_artwork_handoff',
                'reference' => $orderNumber,
                'flow_job_id' => (int) $job->id,
                'task_id' => (int) $handoffTask->id,
                'document_id' => (int) $document->id,
                'document_ids' => $documents->pluck('id')->map(fn ($id) => (int) $id)->all(),
            ],
        );

        // Workflow handoff email is synchronous because task completion depends
        // on provider acceptance. Retry the same idempotent delivery three times
        // before exposing the manual-send fallback. Reusing one tracking ID is
        // important for providers (such as e2a) that support idempotency keys.
        $trackingId = (string) Str::uuid();
        $attemptsUsed = 0;
        $lastException = null;

        for ($attempt = 1; $attempt <= self::DELIVERY_ATTEMPTS; $attempt++) {
            $attemptsUsed = $attempt;

            try {
                $this->email->deliver($message, $trackingId);
                $lastException = null;
                break;
            } catch (Throwable $exception) {
                $lastException = $exception;

                Log::warning('flowtrack.order_workflow_email.retry', [
                    'tracking_id' => $trackingId,
                    'flow_job_id' => (int) $job->id,
                    'task_id' => (int) $handoffTask->id,
                    'document_id' => (int) $document->id,
                    'attempt' => $attempt,
                    'max_attempts' => self::DELIVERY_ATTEMPTS,
                ]);

                // Small bounded backoff gives transient provider/network errors
                // a chance to recover without making the UI unnecessarily slow.
                if ($attempt < self::DELIVERY_ATTEMPTS) {
                    usleep(250_000 * $attempt);
                }
            }
        }

        if ($lastException) {
            report($lastException);
            throw $lastException;
        }


        $job->activities()->create([
            'user_id' => $actor->id,
            'event' => $key === self::PURCHASE_ORDER_HANDOFF
                ? 'job.purchase_order_emailed_to_artwork_team'
                : 'job.artwork_emailed_to_order_team',
            'description' => ($key === self::PURCHASE_ORDER_HANDOFF ? 'Purchase Order' : 'Artwork')
                .' emailed to '.$team.' with '.$documents->pluck('name')->implode(', ').'.',
            'meta' => [
                'task_id' => (int) $handoffTask->id,
                'document_id' => (int) $document->id,
                'document_ids' => $documents->pluck('id')->map(fn ($id) => (int) $id)->all(),
                'document_count' => $documents->count(),
                'document_version' => (int) ($document->version ?: 1),
                'recipient_count' => $recipients->count(),
                'business_unit' => $key === self::ARTWORK_HANDOFF ? $this->orderBusinessUnit($job) : null,
                'tracking_id' => $trackingId,
                'delivery_attempts' => $attemptsUsed,
            ],
        ]);

        return $trackingId;
    }


    /** @return Collection<int,User> */
    private function recipients(FlowJob $job, string $handoffKey): Collection
    {
        return match ($handoffKey) {
            self::PURCHASE_ORDER_HANDOFF => $this->artworkPhaseAssignees($job),
            self::ARTWORK_HANDOFF => $this->orderTeamRoleMembers($job),
            default => collect(),
        };
    }

    /**
     * Purchase Order -> Artwork Team
     *
     * The Artwork Team for an Order is the set of people actually assigned to
     * tasks in that Order's Artwork phase. This intentionally follows runtime
     * task assignment rather than a department/role name, so the email always
     * goes to the people who are responsible for Artwork on this specific
     * Order. Every unique active assignee with a valid email is included.
     *
     * @return Collection<int,User>
     */
    private function artworkPhaseAssignees(FlowJob $job): Collection
    {
        // Locate the Artwork phase from the known Artwork workflow tasks rather
        // than relying on the displayed phase name. This remains safe if the
        // phase is renamed in Order Workflow Setup.
        $artworkPhaseIds = Task::query()
            ->where('flow_job_id', $job->id)
            ->with('setupTemplate:id,automation_key')
            ->get(['id', 'flow_job_id', 'workflow_phase_id', 'task_pack_task_id', 'title'])
            ->filter(function (Task $task): bool {
                return in_array($this->automationKey($task), [
                    'ART_PREPARE_UPLOAD',
                    'ART_INTERNAL_REVIEW',
                    self::ARTWORK_HANDOFF,
                    'ART_CLIENT_ERP_DECISION',
                    'ART_SAMPLE_APPROVAL',
                ], true);
            })
            ->pluck('workflow_phase_id')
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($artworkPhaseIds->isEmpty()) return collect();

        return Task::query()
            ->where('flow_job_id', $job->id)
            ->whereIn('workflow_phase_id', $artworkPhaseIds->all())
            ->whereNotNull('assignee_id')
            ->with('assignee')
            ->orderBy('id')
            ->get()
            ->map(fn (Task $task) => $task->assignee)
            ->filter(fn ($user) => $this->isDeliverableUser($user))
            ->unique(fn (User $user) => mb_strtolower(trim((string) $user->email)))
            ->sortBy(fn (User $user) => mb_strtolower((string) $user->name))
            ->values();
    }

    /**
     * Artwork -> Order Team
     *
     * Order Team membership is owned by Administration > Users & role
     * assignments. Every active user assigned an active "Order Team" role in
     * the current workspace is then filtered by the Order client's business unit:
     * IID Orders -> IID or Both; NEP Orders -> NEP or Both. The role can be represented
     * by its normal name, slug or code; normalization keeps the lookup stable
     * across "Order Team", "order-team" and "ORDER_TEAM" forms.
     *
     * @return Collection<int,User>
     */
    private function orderTeamRoleMembers(FlowJob $job): Collection
    {
        $workspaceId = app(SetupContext::class)->workspaceId();
        $businessUnit = $this->orderBusinessUnit($job);

        $roleIds = Role::query()
            ->where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->get(['id', 'name', 'slug', 'code'])
            ->filter(function (Role $role): bool {
                return collect([$role->name, $role->slug, $role->code])
                    ->filter(fn ($value) => filled($value))
                    ->contains(fn ($value) => $this->normalizeTeamIdentity((string) $value) === 'orderteam');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($roleIds->isEmpty()) return collect();

        return User::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function ($query) use ($roleIds): void {
                // user_roles is the canonical Users & role assignments source.
                // role_id remains a compatibility fallback for older accounts
                // that pre-date the multi-role pivot migration.
                $query->whereHas('roles', fn ($roles) => $roles->whereIn('roles.id', $roleIds->all()))
                    ->orWhereIn('role_id', $roleIds->all());
            })
            ->whereHas('workspaceMemberships', function ($query) use ($workspaceId, $businessUnit): void {
                $query
                    ->where('workspace_id', $workspaceId)
                    ->where('status', 'active')
                    ->where(function ($membership) use ($businessUnit): void {
                        if ($businessUnit !== null) {
                            $membership->where(function ($unitScope) use ($businessUnit): void {
                                $unitScope->whereIn('business_unit', [$businessUnit, 'both'])
                                    ->orWhereNull('business_unit');
                            });
                            return;
                        }

                        // Older/unknown client codes are intentionally restricted
                        // to users explicitly available to both business units.
                        $membership->where(function ($fallback): void {
                            $fallback->where('business_unit', 'both')
                                ->orWhereNull('business_unit');
                        });
                    });
            })
            ->with('roles:id,name,slug,code')
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user) => $this->isDeliverableUser($user))
            ->unique(fn (User $user) => mb_strtolower(trim((string) $user->email)))
            ->values();
    }

    private function normalizeTeamIdentity(string $value): string
    {
        return mb_strtolower((string) preg_replace('/[^a-z0-9]+/i', '', trim($value)));
    }

    /**
     * Resolve the Order business unit from its client. Users & role assignments
     * stores business-unit availability on the active workspace membership as
     * iid, nep or both. A person marked both must receive handoffs for either
     * client, while an IID-only/NEP-only person only receives matching Orders.
     */
    private function orderBusinessUnit(FlowJob $job): ?string
    {
        $job->loadMissing('client');

        $code = $this->normalizeTeamIdentity((string) ($job->client?->code ?? ''));
        if ($code === 'iid') return 'iid';
        if ($code === 'nep') return 'nep';

        // Compatibility fallback for legacy client rows whose code may be blank
        // but whose display name still contains the business-unit identifier.
        $name = $this->normalizeTeamIdentity((string) ($job->client?->name ?? ''));
        if (str_contains($name, 'iid')) return 'iid';
        if (str_contains($name, 'nep')) return 'nep';

        return null;
    }

    private function recipientSourceLabel(string $key, ?FlowJob $job = null): string
    {
        if ($key === self::PURCHASE_ORDER_HANDOFF) {
            return "All assignees in this Order's Artwork phase";
        }

        $businessUnit = $job ? $this->orderBusinessUnit($job) : null;
        if ($businessUnit !== null) {
            return 'Users & role assignments — Order Team role + '.strtoupper($businessUnit).' business unit ('.strtoupper($businessUnit).' or Both)';
        }

        return 'Users & role assignments — Order Team role + Both business units';
    }

    private function missingOrderTeamRecipientMessage(FlowJob $job): string
    {
        $businessUnit = $this->orderBusinessUnit($job);
        if ($businessUnit === null) {
            return 'No active Order Team recipient could be resolved for this client. Ensure the client code is IID or NEP, or assign an active Order Team user with Business unit set to Both IID & NEP and a valid email address.';
        }

        $unitLabel = strtoupper($businessUnit);
        return 'No active '.$unitLabel.' Order Team email address could be resolved from Users & role assignments. '
            .'Assign the Order Team role and set Business unit to '.$unitLabel.' or Both IID & NEP for at least one active user with a valid email address.';
    }

    /** @return Collection<int,Document> */
    private function sourceDocuments(FlowJob $job, string $handoffKey): Collection
    {
        $sourceKey = self::SOURCE_TASK_KEYS[$handoffKey] ?? null;
        if (! $sourceKey) return collect();

        $sourceTaskIds = $this->tasksForAutomationKey($job, $sourceKey)->pluck('id');
        if ($sourceTaskIds->isEmpty()) return collect();

        $documents = Document::query()
            ->where('flow_job_id', $job->id)
            ->whereIn('task_id', $sourceTaskIds)
            ->orderBy('id')
            ->get();

        if ($documents->isEmpty()) return collect();
        if ($sourceKey !== 'ART_PREPARE_UPLOAD') return collect([$documents->last()]);

        $latestVersion = max(1, (int) $documents->max('version'));

        return $documents->where('version', $latestVersion)->values();
    }

    /** @return Collection<int,Task> */
    private function tasksForAutomationKey(FlowJob $job, string $automationKey): Collection
    {
        return Task::query()
            ->where('flow_job_id', $job->id)
            ->with(['setupTemplate.defaultDepartment', 'setupTemplate.defaultAssignee', 'assignee.department'])
            ->orderBy('id')
            ->get()
            ->filter(fn (Task $task) => $this->automationKey($task) === $automationKey)
            ->values();
    }

    private function automationKey(Task $task): ?string
    {
        $task->loadMissing('setupTemplate');
        $key = trim((string) ($task->setupTemplate?->automation_key ?? ''));
        if ($key !== '') return $key;

        $title = mb_strtolower(trim((string) $task->title));
        foreach (self::SOURCE_TASK_TITLES as $candidateKey => $candidateTitle) {
            if ($title === mb_strtolower($candidateTitle)) return $candidateKey;
        }
        if ($title === 'prepare and upload artwork') return 'ART_PREPARE_UPLOAD';

        if ($title === 'send purchase order to artwork team') return self::PURCHASE_ORDER_HANDOFF;
        if ($title === 'send artwork to order team') return self::ARTWORK_HANDOFF;

        return null;
    }

    private function isDeliverableUser(mixed $user): bool
    {
        return $user instanceof User
            && (bool) $user->is_active
            && filter_var(trim((string) $user->email), FILTER_VALIDATE_EMAIL) !== false;
    }

    private function teamLabel(string $key): string
    {
        return $key === self::PURCHASE_ORDER_HANDOFF ? 'Artwork Team' : 'Order Team';
    }

    private function subject(FlowJob $job, string $key): string
    {
        $orderNumber = $job->displayOrderNumber();

        return $key === self::PURCHASE_ORDER_HANDOFF
            ? 'Purchase Order ready — '.$orderNumber
            : 'Artwork ready — '.$orderNumber;
    }

    /** @return array<string,mixed> */
    /** @param Collection<int,Document> $documents */
    private function viewData(FlowJob $job, string $key, Document $document, Collection $documents, User $actor, array $brand): array
    {
        return [
            'brand' => $brand,
            'job' => $job,
            'team' => $this->teamLabel($key),
            'handoffType' => $key === self::PURCHASE_ORDER_HANDOFF ? 'purchase_order' : 'artwork',
            'document' => $document,
            'documents' => $documents,
            'sentBy' => $actor,
            'orderNumber' => $job->displayOrderNumber(),
            'productSummary' => $this->productSummary($job),
        ];
    }

    private function senderAddress(): string
    {
        if (mb_strtolower((string) config('flowtrack_email.transport', 'laravel')) === 'e2a') {
            return trim((string) config('flowtrack_email.e2a.agent_email'));
        }

        return trim((string) config('mail.from.address'));
    }

    /** @param array<string,mixed> $brand */
    private function senderName(array $brand): string
    {
        if (mb_strtolower((string) config('flowtrack_email.transport', 'laravel')) === 'e2a') {
            return '';
        }

        $configured = trim((string) config('mail.from.name'));
        if ($configured !== '' && ! in_array(mb_strtolower($configured), ['laravel', 'flowtrack'], true)) {
            return $configured;
        }

        return trim((string) ($brand['name'] ?? '')) ?: 'Company';
    }

    private function deliveryLabel(): string
    {
        $transport = mb_strtolower(trim((string) config('flowtrack_email.transport', 'laravel')));
        if ($transport === 'e2a') return 'E2A email service';

        $mailer = trim((string) config('flowtrack_email.mailer', config('mail.default')));
        return $mailer !== '' ? strtoupper($mailer).' mailer' : 'Configured email service';
    }

    private function productSummary(FlowJob $job): string
    {
        $items = $job->relationLoaded('items') ? $job->items : $job->items()->get();
        $names = $items
            ->filter(fn ($item) => ! ($item->is_removed ?? false))
            ->map(fn ($item) => trim((string) ($item->product_name ?? '')))
            ->filter()
            ->unique()
            ->values();

        if ($names->isEmpty()) return trim((string) ($job->product ?? '')) ?: 'Order products';
        if ($names->count() <= 2) return $names->implode(', ');

        return $names->take(2)->implode(', ').' +'.($names->count() - 2).' more';
    }

    /** @return array<string,mixed> */
    private function companyBrand(): array
    {
        $branding = $this->branding->current();
        $profile = $this->companyProfile->current();
        $tradingName = trim((string) ($profile['trading_name'] ?? ''));
        $legalName = trim((string) ($profile['legal_name'] ?? ''));
        $displayName = $tradingName !== ''
            ? $tradingName
            : ($legalName !== '' ? $legalName : trim((string) ($branding['name'] ?? '')));

        return array_merge($branding, [
            'name' => $displayName !== '' ? $displayName : 'Company',
            'legal_name' => $legalName,
            'trading_name' => $tradingName,
            'registration_number' => trim((string) ($profile['registration_number'] ?? '')),
            'tax_number' => trim((string) ($profile['tax_number'] ?? '')),
            'billing_email' => trim((string) ($profile['billing_email'] ?? '')),
            'phone' => trim((string) ($profile['phone'] ?? '')),
            'website' => trim((string) ($profile['website'] ?? '')),
            'address_lines' => $this->companyProfile->addressLines($profile),
        ]);
    }
}
