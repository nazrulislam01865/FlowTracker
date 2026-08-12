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
