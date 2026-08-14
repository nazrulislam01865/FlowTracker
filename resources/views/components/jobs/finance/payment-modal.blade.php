@props(['job'])
<div class="ft-finance-modal-backdrop" wire:key="record-payment-modal" wire:click.self="closeRecordPayment">
    <section class="ft-finance-modal ft-finance-small-modal" role="dialog" aria-modal="true" aria-labelledby="recordPaymentTitle">
        <header class="ft-finance-modal-head"><div><h2 id="recordPaymentTitle">Record payment</h2><p>Apply a received payment to an invoice for this order.</p></div><button type="button" wire:click="closeRecordPayment" aria-label="Close">×</button></header>
        @error('paymentForm')<div class="ft-finance-form-alert">{{ $message }}</div>@enderror
        <div class="ft-finance-small-grid">
            <label class="wide"><span>Invoice <b>*</b></span><select wire:model.live="paymentInvoiceId"><option value="">Select invoice</option>@foreach($job->invoices as $invoice)@if(!in_array($invoice->status,['draft','cancelled','paid'],true) && $invoice->balanceAmount()>0)<option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} · {{ $invoice->currency }} {{ number_format($invoice->balanceAmount(),2) }} due</option>@endif @endforeach</select>@error('paymentInvoiceId')<small class="error">{{ $message }}</small>@enderror</label>
            <label><span>Payment date <b>*</b></span><input type="date" wire:model="paymentDate">@error('paymentDate')<small class="error">{{ $message }}</small>@enderror</label>
            <label><span>Method <b>*</b></span><select wire:model="paymentMethod"><option>Bank transfer</option><option>Credit card</option><option>Cash</option><option>Cheque</option><option>Other</option></select>@error('paymentMethod')<small class="error">{{ $message }}</small>@enderror</label>
            <label><span>Amount <b>*</b></span><input type="number" step="0.01" min="0.01" wire:model="paymentAmount">@error('paymentAmount')<small class="error">{{ $message }}</small>@enderror</label>
            <label><span>Reference</span><input type="text" wire:model="paymentReference" placeholder="Bank reference"></label>
            <label class="wide"><span>Notes</span><textarea wire:model="paymentNotes" placeholder="Optional payment note"></textarea></label>
        </div>
        <footer class="ft-finance-modal-foot"><span></span><div><button type="button" class="secondary" wire:click="closeRecordPayment">Cancel</button><button type="button" class="primary" wire:click="recordPayment" wire:loading.attr="disabled" wire:target="recordPayment">Record payment</button></div></footer>
    </section>
</div>
