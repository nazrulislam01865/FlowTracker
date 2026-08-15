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

    protected $guarded = [];

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

    public function productDisplayCode(): string
    {
        if ($this->type !== 'product') return trim((string) $this->code);

        return 'PRD-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
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
