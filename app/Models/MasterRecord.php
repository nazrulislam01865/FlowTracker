<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'parent_id',
        'type',
        'code',
        'name',
        'description',
        'metadata',
        'status',
        'sort_order',
        'color',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'sort_order' => 'integer'];
    }

    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name'); }

    public function scopeForWorkspace(Builder $query, int $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getIsActiveAttribute(): bool { return $this->status === 'active'; }



    public function inquiryAutoStatus(): string
    {
        if ($this->type !== 'inquiry_task_status') return trim((string) $this->name);

        $configured = trim((string) data_get($this->metadata, 'auto_inquiry_status'));
        if ($configured === '__task_status__' || $configured === '') {
            return trim((string) $this->name);
        }

        return $configured;
    }

    public function requiresAttention(): bool
    {
        return $this->type === 'inquiry_task_status'
            && filter_var(data_get($this->metadata, 'requires_attention', false), FILTER_VALIDATE_BOOL);
    }



    public function orderTaskFlagId(): ?int
    {
        if ($this->type !== 'order_task_status') return null;
        $id = (int) data_get($this->metadata, 'order_task_flag_id', 0);
        return $id > 0 ? $id : null;
    }

    public function orderFlagId(): ?int
    {
        if ($this->type !== 'order_task_flag') return null;
        $id = (int) data_get($this->metadata, 'order_flag_id', 0);
        return $id > 0 ? $id : null;
    }

    public function systemKey(): ?string
    {
        $key = trim((string) data_get($this->metadata, 'system_key'));
        return $key !== '' ? $key : null;
    }

    public function taskPackWorkCalendarDayRange(): string
    {
        if ($this->type !== 'task_pack_work_calendar') return '';

        $labels = [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun',
        ];

        $from = strtolower(trim((string) data_get($this->metadata, 'day_from')));
        $to = strtolower(trim((string) data_get($this->metadata, 'day_to')));

        if ($from === '' && $to === '' && $this->code === 'TPW-001') {
            $from = 'monday';
            $to = 'friday';
        }

        if ($from === '' || $to === '') return '—';
        if ($from === $to) return $labels[$from] ?? ucfirst($from);

        return ($labels[$from] ?? ucfirst($from)).'–'.($labels[$to] ?? ucfirst($to));
    }

    public function taskPackWorkCalendarTimeRange(): string
    {
        if ($this->type !== 'task_pack_work_calendar') return '';

        $from = trim((string) data_get($this->metadata, 'time_from'));
        $to = trim((string) data_get($this->metadata, 'time_to'));

        if ($from === '' && $to === '' && $this->code === 'TPW-001') {
            $from = '09:00';
            $to = '18:00';
        }

        return $from !== '' && $to !== '' ? $from.'–'.$to : '—';
    }

    public function taskPackWorkCalendarLabel(): string
    {
        if ($this->type !== 'task_pack_work_calendar') return trim((string) $this->name);

        $name = trim((string) $this->name);
        $days = $this->taskPackWorkCalendarDayRange();
        $times = $this->taskPackWorkCalendarTimeRange();

        // Legacy seeded records already contained the schedule inside the name.
        // Keep them readable without duplicating that schedule until migrated.
        if (str_contains($name, '·')) return $name;

        $schedule = collect([$days !== '—' ? $days : null, $times !== '—' ? $times : null])
            ->filter()
            ->implode(', ');

        return $schedule !== '' ? $name.' · '.$schedule : $name;
    }

    public function productDisplayCode(): string
    {
        if ($this->type !== 'product') return trim((string) $this->code);

        return 'PRD-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Default supplier configured on the Product master record.
     *
     * Products can be linked to multiple suppliers through metadata.supplier_ids.
     * supplier_id remains the explicit default used by Create Order/Inquiry, while
     * default_supplier_id is retained as a legacy read fallback only.
     */
    public function productSupplierId(): ?int
    {
        if ($this->type !== 'product') return null;

        $id = (int) (
            data_get($this->metadata, 'supplier_id')
            ?: data_get($this->metadata, 'default_supplier_id')
            ?: 0
        );

        return $id > 0 ? $id : null;
    }

    /** @return array<int,int> */
    public function productSupplierIds(): array
    {
        if ($this->type !== 'product') return [];

        $raw = data_get($this->metadata, 'supplier_ids', []);

        // Be tolerant of older/imported records where the nested supplier list
        // was stored as a JSON string or a comma/space separated scalar instead
        // of a decoded JSON array. This keeps supplier counts and filters truthful
        // without requiring a database migration just to read existing links.
        if (is_string($raw)) {
            $trimmed = trim($raw);
            $decoded = $trimmed !== '' ? json_decode($trimmed, true) : null;
            $raw = is_array($decoded)
                ? $decoded
                : (preg_split('/[\s,;|]+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        } elseif (is_int($raw) || is_float($raw)) {
            $raw = [$raw];
        } elseif (! is_array($raw)) {
            $raw = [];
        }

        $linked = collect($raw)
            ->flatten()
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0);

        $defaultId = $this->productSupplierId();
        if ($defaultId) $linked->prepend($defaultId);

        return $linked->unique()->values()->all();
    }

    public function hasProductSupplier(int $supplierId): bool
    {
        return $supplierId > 0 && in_array($supplierId, $this->productSupplierIds(), true);
    }

    /** @return array<int, array{quantity:int, price:float}> */
    public function productPriceBreakpoints(): array
    {
        if ($this->type !== 'product') return [];

        $rows = $this->normalizedProductPriceRows(data_get($this->metadata, 'price_breakpoints', []));
        if ($rows !== []) return $rows;

        // Backward/repair fallback: older Product rows can still contain the
        // original pasted Excel table even if the normalized breakpoint array
        // was not persisted correctly. Re-parse that source so Product Details
        // always shows the saved pricing instead of silently hiding the section.
        $raw = trim((string) data_get($this->metadata, 'price_table_raw'));
        if ($raw === '') return [];

        try {
            return app(\App\Services\ProductPriceTableParser::class)->parseTable($raw)['price_breakpoints'] ?? [];
        } catch (\Throwable $exception) {
            report($exception);
            return [];
        }
    }

    public function productPriceForQuantity(int|float $quantity): ?float
    {
        if ($this->type !== 'product' || $quantity <= 0) return null;

        $matchedPrice = null;
        foreach ($this->productPriceBreakpoints() as $breakpoint) {
            if ($breakpoint['quantity'] > $quantity) break;
            $matchedPrice = (float) $breakpoint['price'];
        }

        return $matchedPrice;
    }

    /** @return array<int, array{quantity:int, price:float}> */
    public function productRemoteSurchargeBreakpoints(): array
    {
        if ($this->type !== 'product') return [];

        $rows = $this->normalizedProductPriceRows(data_get($this->metadata, 'remote_surcharge_breakpoints', []));
        if ($rows !== []) return $rows;

        $raw = trim((string) data_get($this->metadata, 'price_table_raw'));
        if ($raw === '') return [];

        try {
            return app(\App\Services\ProductPriceTableParser::class)->parseTable($raw)['remote_surcharge_breakpoints'] ?? [];
        } catch (\Throwable $exception) {
            report($exception);
            return [];
        }
    }

    /** @return array<int, array{quantity:int, price:float}> */
    private function normalizedProductPriceRows(mixed $value): array
    {
        // Be tolerant of legacy/imported rows where JSON was stored as a string
        // inside metadata instead of as an already-decoded array.
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return collect(is_array($value) ? $value : [])
            ->map(fn ($row) => [
                'quantity' => (int) data_get($row, 'quantity', 0),
                'price' => (float) data_get($row, 'price', -1),
            ])
            ->filter(fn ($row) => $row['quantity'] > 0 && $row['price'] >= 0)
            ->sortBy('quantity')
            ->values()
            ->all();
    }

    public function productRemoteSurchargeForQuantity(int|float $quantity): ?float
    {
        if ($this->type !== 'product' || $quantity <= 0) return null;

        $matchedPrice = null;
        foreach ($this->productRemoteSurchargeBreakpoints() as $breakpoint) {
            if ($breakpoint['quantity'] > $quantity) break;
            $matchedPrice = (float) $breakpoint['price'];
        }

        return $matchedPrice;
    }

    public function productReferenceCode(): string
    {
        $metadata = (array) ($this->metadata ?? []);
        if (array_key_exists('reference_code', $metadata)) {
            return trim((string) $metadata['reference_code']);
        }

        return trim((string) $this->code);
    }

    public function productMainCategory(): string
    {
        if ($this->type !== 'product') return '';

        return trim((string) (
            data_get($this->metadata, 'main_category')
            ?: data_get($this->metadata, 'excel_main_category')
            ?: data_get($this->parent?->metadata, 'excel_main_category')
            ?: $this->parent?->name
            ?: 'Uncategorized'
        ));
    }

    public function productClassificationPath(): string
    {
        if ($this->type !== 'product') return '';

        $category = trim((string) ($this->parent?->name ?? data_get($this->metadata, 'category') ?? data_get($this->metadata, 'excel_category')));
        $subCategory = trim((string) (data_get($this->metadata, 'sub_category') ?: data_get($this->metadata, 'excel_sub_category') ?: $this->productCatalogSummary()));

        return collect([$category, $subCategory])->filter()->unique()->implode(' > ');
    }

    public function productSize(): string
    {
        if ($this->type !== 'product') return '';

        return trim((string) (data_get($this->metadata, 'product_size') ?? ''));
    }

    /** @return array<int,string> */
    public function productAvailabilityLabels(): array
    {
        if ($this->type !== 'product') return [];

        $metadata = (array) ($this->metadata ?? []);
        $labels = $metadata['client_availability_labels'] ?? $metadata['client_codes'] ?? $metadata['clients'] ?? null;

        if (is_array($labels)) {
            $labels = array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $labels)));
            return $labels ?: ['All clients'];
        }

        $availability = trim((string) ($metadata['client_availability'] ?? ''));
        if ($availability === '' || in_array(strtolower($availability), ['all', 'all clients'], true)) {
            return ['All clients'];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[,|]+/', $availability) ?: []))) ?: ['All clients'];
    }

    public function hasSpecificProductAvailability(): bool
    {
        $labels = $this->productAvailabilityLabels();

        return ! ($labels === [] || (count($labels) === 1 && strtolower($labels[0]) === 'all clients'));
    }

    /** @return array<int, array{key:string,shipment_urgency_id:int,shipment_urgency_code:string,shipment_urgency_name:string,extra_charge:float}> */
    public function productShipmentUrgencyOptions(): array
    {
        if ($this->type !== 'product') return [];

        return collect((array) data_get($this->metadata, 'shipment_urgency_options', []))
            ->filter(fn ($option) => is_array($option))
            ->map(function (array $option): array {
                return [
                    'key' => trim((string) ($option['key'] ?? '')),
                    'shipment_urgency_id' => (int) ($option['shipment_urgency_id'] ?? 0),
                    'shipment_urgency_code' => trim((string) ($option['shipment_urgency_code'] ?? '')),
                    'shipment_urgency_name' => trim((string) ($option['shipment_urgency_name'] ?? '')),
                    'extra_charge' => max(0, (float) ($option['extra_charge'] ?? 0)),
                ];
            })
            ->filter(fn ($option) => $option['shipment_urgency_id'] > 0)
            ->values()
            ->all();
    }

    /** @return array<int, array{key:string,label:string,extra_charge:float,image_url:?string}> */
    public function productOptions(): array
    {
        if ($this->type !== 'product') return [];

        return collect((array) data_get($this->metadata, 'product_options', []))
            ->filter(fn ($option) => is_array($option))
            ->map(function (array $option): array {
                $key = trim((string) ($option['key'] ?? ''));
                $label = trim((string) ($option['label'] ?? ''));
                $path = trim((string) ($option['image_path'] ?? ''));
                $imageUrl = null;

                if ($this->id && $key !== '' && $path !== '') {
                    $imageUrl = route('master-data.product-option-image', [
                        'product' => $this->id,
                        'optionKey' => $key,
                        'filename' => basename($path),
                    ], false);
                }

                return [
                    'key' => $key,
                    'label' => $label,
                    'extra_charge' => max(0, (float) ($option['extra_charge'] ?? 0)),
                    'image_url' => $imageUrl,
                ];
            })
            ->filter(fn ($option) => $option['key'] !== '' && $option['label'] !== '')
            ->values()
            ->all();
    }

    public function productOptionExtraCharge(string $optionKey): float
    {
        $optionKey = trim($optionKey);
        if ($this->type !== 'product' || $optionKey === '') return 0.0;

        foreach ($this->productOptions() as $option) {
            if ((string) $option['key'] === $optionKey) {
                return max(0, (float) ($option['extra_charge'] ?? 0));
            }
        }

        return 0.0;
    }

    /** @param array<int,string> $optionKeys */
    public function productOptionsExtraCharge(array $optionKeys): float
    {
        if ($this->type !== 'product' || $optionKeys === []) return 0.0;

        $wanted = array_values(array_unique(array_filter(array_map(
            static fn ($value) => trim((string) $value),
            $optionKeys
        ))));

        if ($wanted === []) return 0.0;

        return (float) collect($this->productOptions())
            ->filter(fn ($option) => in_array((string) $option['key'], $wanted, true))
            ->sum(fn ($option) => max(0, (float) ($option['extra_charge'] ?? 0)));
    }

    /** @param array<int,string> $optionKeys */
    public function productPriceForQuantityWithOptions(int|float $quantity, array $optionKeys = []): ?float
    {
        $basePrice = $this->productPriceForQuantity($quantity);
        if ($basePrice === null) return null;

        return $basePrice + $this->productOptionsExtraCharge($optionKeys);
    }

    /** @return array<int,array{kind:string,label:string,url:?string,download_url:?string}> */
    public function productDocuments(): array
    {
        if ($this->type !== 'product') return [];

        $metadata = (array) ($this->metadata ?? []);
        $documents = [];
        $definitions = [
            'certificate' => ['certificate_test_report', 'certificate_test_report_url', 'certificate_test_report_path'],
            'template' => ['template_doc', 'template_doc_url', 'template_doc_path'],
        ];

        foreach ($definitions as $kind => [$labelKey, $urlKey, $pathKey]) {
            $label = trim((string) ($metadata[$labelKey] ?? ''));
            $url = trim((string) ($metadata[$urlKey] ?? ''));
            $path = trim((string) ($metadata[$pathKey] ?? ''));
            if ($label === '' && $url === '' && $path === '') continue;

            if ($path !== '') {
                $filename = basename($path);
                $url = route('master-data.product-document', [
                    'product' => $this->id,
                    'kind' => $kind,
                    'filename' => $filename,
                ], false);
                $label = $label !== '' ? $label : $filename;
            }

            $documents[] = [
                'kind' => $kind,
                'label' => $label !== '' ? $label : basename(parse_url($url, PHP_URL_PATH) ?: $url),
                'url' => $url !== '' ? $url : null,
                'download_url' => $path !== '' ? $url.'?download=1' : ($url !== '' ? $url : null),
            ];
        }

        return $documents;
    }

    public function productImageUrl(): ?string
    {
        if (! $this->id || $this->type !== 'product') return null;

        $path = trim((string) data_get($this->metadata, 'product_image_path'));
        if ($path === '') return null;

        return route('master-data.product-image', [
            'product' => $this->id,
            'filename' => basename($path),
        ], false);
    }

    /**
     * Product search cards must show product-specific details, not repeat the
     * Product Category. Older FlowTrack demo/import rows stored values such as
     * "Caps · Embroidery" in description before parent_id became the
     * canonical category relationship. Keep the stored value untouched, but
     * remove only that duplicated leading category when displaying catalog
     * search results.
     */
    public function productCatalogSummary(): ?string
    {
        if ($this->type !== 'product') return null;

        $summary = trim(strip_tags((string) $this->description));
        if ($summary === '') return null;

        $category = trim((string) ($this->parent?->name ?? ''));
        if ($category === '') return $summary;

        if (mb_strtolower($summary) === mb_strtolower($category)) return null;

        $pattern = '/^'.preg_quote($category, '/').'\s*(?:·|\-|—|:)\s*/iu';
        $cleaned = preg_replace($pattern, '', $summary, 1);
        $cleaned = trim((string) $cleaned);

        return $cleaned !== '' ? $cleaned : null;
    }
}
