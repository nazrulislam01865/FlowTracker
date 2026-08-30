@props([
    'revisionNote',
    'canExportDocument' => false,
])
@php
    $revisionComment = trim((string) data_get($revisionNote->meta, 'revision_comment', ''));
    if ($revisionComment === '') {
        $description = (string) $revisionNote->description;
        $revisionComment = str_contains($description, ': ')
            ? \Illuminate\Support\Str::after($description, ': ')
            : $description;
    }

    $referenceDocument = $revisionNote->relationLoaded('referenceDocument')
        ? $revisionNote->getRelation('referenceDocument')
        : null;
    $extension = $referenceDocument
        ? strtoupper(pathinfo((string) $referenceDocument->name, PATHINFO_EXTENSION) ?: 'FILE')
        : 'FILE';
@endphp

<section class="ft-order-artwork-revision-card" wire:key="order-task-artwork-revision-{{ $revisionNote->id }}">
    <div class="ft-order-artwork-revision-head">
        <span class="ft-order-artwork-revision-alert" aria-hidden="true">
            <span>!</span>
        </span>

        <div class="ft-order-artwork-revision-heading">
            <div class="ft-order-artwork-revision-kicker">Revision required</div>
            <div class="ft-order-artwork-revision-title">Artwork revision issue</div>
            <div class="ft-order-artwork-revision-meta">
                Requested by {{ $revisionNote->user?->name ?? 'FlowTrack' }} · {{ \App\Support\UserLocalTime::format($revisionNote->created_at, 'M j, Y, g:i A') }}
            </div>
        </div>

        <span class="ft-order-artwork-revision-badge">Revision</span>
    </div>

    <div class="ft-order-artwork-revision-grid">
        <div class="ft-order-artwork-revision-field">
            <div class="ft-order-artwork-revision-label">Required change</div>
            <div class="ft-order-artwork-revision-change">{{ $revisionComment }}</div>
        </div>

        <div class="ft-order-artwork-revision-field">
            <div class="ft-order-artwork-revision-label">Reference attachment</div>

            @if($referenceDocument)
                <div class="ft-order-artwork-revision-attachment">
                    <span class="file-icon ft-order-file-icon ft-order-artwork-revision-file-icon">{{ $extension }}</span>
                    <span class="ft-order-artwork-revision-file-copy">
                        <b>{{ $referenceDocument->name }} · Version {{ max(1, (int) $referenceDocument->version) }}</b>
                        <small>{{ $extension }} · Uploaded {{ \App\Support\UserLocalTime::format($referenceDocument->created_at, 'M j, Y, g:i A') }}</small>
                    </span>
                    <span class="ft-order-artwork-revision-file-actions">
                        <a href="{{ route('documents.open', $referenceDocument) }}" target="_blank" rel="noopener">Open</a>
                        @if($canExportDocument)
                            <span class="ft-order-artwork-revision-action-divider" aria-hidden="true"></span>
                            <a href="{{ route('documents.download', $referenceDocument) }}">Download</a>
                        @endif
                    </span>
                </div>
            @else
                <div class="ft-order-artwork-revision-attachment ft-order-artwork-revision-attachment--empty">
                    No reference attachment was available for this revision request.
                </div>
            @endif
        </div>
    </div>
</section>
