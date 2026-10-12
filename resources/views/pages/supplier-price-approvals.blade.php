@extends('layouts.app')
@section('content')
<style>
    .ft-price-approvals{max-width:1240px;margin:0 auto;padding:25px;color:#17253e}
    .ft-price-approvals h1{font-size:25px;font-weight:700;margin:0 0 8px}
    .ft-price-approvals .ft-price-description{color:#64748b;margin:0 0 22px}
    .ft-price-approvals .ft-approval-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}
    .ft-price-approvals h2{font-size:18px;font-weight:700;margin:0}
    .ft-price-approvals .ft-count{font-size:13px;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;padding:5px 11px;border-radius:16px}
    .ft-price-approvals .ft-approval-table{width:100%;border-collapse:collapse;text-align:left;background:#fff}
    .ft-price-approvals .ft-table-shell{border:1px solid #e0e6ed;border-radius:9px;overflow-x:auto;background:#fff}
    .ft-price-approvals th{padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e1e8ef;font-size:12px;font-weight:700;color:#52647a;white-space:nowrap}
    .ft-price-approvals td{padding:17px 18px;border-bottom:1px solid #eef1f5;vertical-align:middle;font-size:13px}
    .ft-price-approvals tbody tr:last-child td{border-bottom:0}
    .ft-price-approvals .ft-product-name{font-size:13px;font-weight:700;color:#182b43}
    .ft-price-approvals .ft-subtext{color:#718198;font-size:12px;margin-top:5px}
    .ft-price-approvals .ft-review-button,.ft-price-approvals .ft-action-approve{border:0;border-radius:6px;background:#007f71;color:#fff;font-size:13px;font-weight:700;padding:10px 16px;cursor:pointer}
    .ft-price-approvals .ft-review-button:hover,.ft-price-approvals .ft-action-approve:hover{background:#00675d}
    .ft-price-approvals .ft-status-pill{display:inline-flex;border:1px solid #f0d7aa;background:#fff8e8;color:#8a5b13;padding:5px 9px;border-radius:20px;font-size:12px;font-weight:700}
    .ft-price-approvals .ft-review-notice{padding:13px 15px;border:1px solid #cee5dd;background:#f1f9f5;color:#16634a;margin-bottom:16px;border-radius:7px}
    .ft-price-approvals .ft-review-notice.warning{border-color:#f1dcaf;background:#fff9ea;color:#79561c}
    .ft-price-approvals .ft-empty{padding:44px 20px;text-align:center;color:#64748b}
    .ft-price-approvals .ft-page-links{margin-top:18px}
    .ft-price-approvals dialog.ft-review-modal{border:0;border-radius:12px;padding:0;width:min(690px,calc(100vw - 32px));max-height:calc(100dvh - 48px);overflow-y:auto;color:#1b2b42;box-shadow:0 28px 75px rgba(10,28,50,.23);margin:auto}
    .ft-price-approvals dialog.ft-review-modal::backdrop{background:rgba(11,26,44,.57)}
    .ft-price-approvals .ft-modal-header{padding:22px 25px 17px;border-bottom:1px solid #e7ebf0;display:flex;justify-content:space-between;gap:14px;align-items:flex-start}
    .ft-price-approvals .ft-modal-header h3{font-size:20px;font-weight:700;margin:0 0 5px}
    .ft-price-approvals .ft-modal-close{border:1px solid #d7e0ea;background:#fff;border-radius:6px;padding:6px 11px;font-size:20px;line-height:1;cursor:pointer}
    .ft-price-approvals .ft-modal-body{padding:20px 25px}
    .ft-price-approvals .ft-review-table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
    .ft-price-approvals .ft-review-table th,.ft-price-approvals .ft-review-table td{padding:12px;text-align:right}
    .ft-price-approvals .ft-review-table th:first-child,.ft-price-approvals .ft-review-table td:first-child{text-align:left}
    .ft-price-approvals .ft-review-table .ft-changed{color:#007f71;font-weight:700;background:#f0faf7}
    .ft-price-approvals .ft-removed{color:#9c3333}
    .ft-price-approvals .ft-review-label{font-size:13px;color:#53677d;font-weight:700;margin:0 0 10px}
    .ft-price-approvals .ft-note-label{display:block;font-size:13px;font-weight:700;margin:20px 0 7px}
    .ft-price-approvals .ft-review-note{width:100%;padding:11px;border:1px solid #cbd5df;border-radius:6px;font:inherit;font-size:13px}
    .ft-price-approvals .ft-modal-footer{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:10px;padding:15px 25px 23px}
    .ft-price-approvals .ft-action-reject,.ft-price-approvals .ft-action-cancel{font-size:13px;font-weight:700;padding:10px 16px;border-radius:6px;cursor:pointer;background:white;border:1px solid #d3dce5;color:#334155}
    .ft-price-approvals .ft-action-reject{color:#b42331;border-color:#e4bfc4}
    @media(max-width:650px){.ft-price-approvals{padding:16px}.ft-price-approvals .ft-approval-table{min-width:650px}.ft-price-approvals .ft-modal-body,.ft-price-approvals .ft-modal-header{padding:16px}.ft-price-approvals .ft-modal-footer{padding:14px 16px}}
</style>
<div class="ft-price-approvals">
    <h1>Supplier price approvals</h1>
    <p class="ft-price-description">Review price changes requested by suppliers. Approved changes affect only the selected supplier; customer selling prices remain unchanged.</p>
    @if(session('success'))<div role="status" class="ft-review-notice">{{ session('success') }}</div>@endif
    @if(session('warning'))<div role="status" class="ft-review-notice warning">{{ session('warning') }}</div>@endif
    <div class="ft-approval-heading">
        <h2>Requests awaiting approval</h2>
        <span class="ft-count">{{ number_format($proposals->total()) }} pending</span>
    </div>
    <div class="ft-table-shell">
        <table class="ft-approval-table">
            <thead><tr><th>Product</th><th>Supplier</th><th>Proposed tiers</th><th>Requested</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($proposals as $proposal)
                    <tr>
                        <td><div class="ft-product-name">{{ $proposal->product_name }}</div><div class="ft-subtext">{{ $proposal->product_display_code }}</div></td>
                        <td>{{ $proposal->supplier_name }}</td>
                        <td>
                            @foreach(array_slice($proposal->proposed_rows, 0, 3) as $tier)
                                <div>{{ number_format((int) $tier['quantity']) }} units → {{ rtrim(rtrim(number_format((float) $tier['price'], 6, '.', ','), '0'), '.') }}</div>
                            @endforeach
                            @if($proposal->new_tier_count > 3)<div class="ft-subtext">+{{ $proposal->new_tier_count - 3 }} more in details</div>@endif
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($proposal->created_at)->format('d M Y, h:i A') }}</td>
                        <td><span class="ft-status-pill">Pending</span></td>
                        <td><button type="button" class="ft-review-button" data-open-price-review="{{ $proposal->id }}">View changes</button></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="ft-empty">No supplier price change requests are awaiting approval.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ft-page-links">{{ $proposals->links() }}</div>
    @foreach($proposals as $proposal)
        <dialog id="ft-price-review-{{ $proposal->id }}" class="ft-review-modal" aria-labelledby="ft-price-review-title-{{ $proposal->id }}">
            <div class="ft-modal-header">
                <div>
                    <h3 id="ft-price-review-title-{{ $proposal->id }}">Review supplier price change</h3>
                    <div class="ft-product-name">{{ $proposal->product_name }}</div>
                    <div class="ft-subtext">{{ $proposal->product_display_code }} · {{ $proposal->supplier_name }}</div>
                </div>
                <button type="button" class="ft-modal-close" data-close-price-review aria-label="Close price review">&times;</button>
            </div>
            <form method="post" action="{{ route('supplier-prices.review', $proposal->id) }}">
                @csrf
                <div class="ft-modal-body">
                    <p class="ft-review-label">Compare the current approved supplier unit prices with the requested prices at each quantity.</p>
                    <div style="overflow-x:auto">
                        <table class="ft-review-table">
                            <thead><tr><th>Quantity</th><th>Current unit price</th><th>Requested unit price</th></tr></thead>
                            <tbody>
                                @foreach($proposal->changes as $change)
                                    <tr>
                                        <td>{{ number_format($change['quantity']) }}</td>
                                        <td>{{ $change['old_price'] === null ? '—' : rtrim(rtrim(number_format((float) $change['old_price'], 6, '.', ','), '0'), '.') }}</td>
                                        <td class="{{ $change['changed'] ? 'ft-changed' : '' }} {{ $change['new_price'] === null ? 'ft-removed' : '' }}">
                                            {{ $change['new_price'] === null ? 'Removed' : rtrim(rtrim(number_format((float) $change['new_price'], 6, '.', ','), '0'), '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <label class="ft-note-label" for="ft-review-note-{{ $proposal->id }}">Decision note (optional)</label>
                    <textarea id="ft-review-note-{{ $proposal->id }}" class="ft-review-note" name="review_note" rows="2" maxlength="500" placeholder="Reason or instructions for the supplier"></textarea>
                </div>
                <div class="ft-modal-footer">
                    <button type="button" class="ft-action-cancel" data-close-price-review>Cancel</button>
                    <button type="submit" class="ft-action-reject" name="decision" value="rejected">Reject</button>
                    <button type="submit" class="ft-action-approve" name="decision" value="approved">Approve changes</button>
                </div>
            </form>
        </dialog>
    @endforeach
</div>
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
document.addEventListener('click', function (event) {
    var open = event.target.closest('[data-open-price-review]');
    if (open) {
        var dialog = document.getElementById('ft-price-review-' + open.dataset.openPriceReview);
        if (dialog && !dialog.open) dialog.showModal();
        return;
    }
    var close = event.target.closest('[data-close-price-review]');
    if (close) close.closest('dialog')?.close();
});
</script>
@endsection
