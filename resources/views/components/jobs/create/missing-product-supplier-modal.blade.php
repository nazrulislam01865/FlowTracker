@if($showMissingProductSupplierModal)
    <x-ui.modal
        id="missing-order-product-supplier"
        title="Supplier not linked"
        size="sm"
        :open="true"
        class="ft-order-product-supplier-missing-modal"
    >
        <x-slot:close>
            <button type="button" class="ft-order-product-supplier-modal-close" wire:click="closeMissingProductSupplierModal" aria-label="Close">&times;</button>
        </x-slot:close>

        <div class="ft-order-product-supplier-modal-message">
            <span class="ft-order-product-supplier-modal-icon" aria-hidden="true">!</span>
            <div>
                @if(trim((string) $missingProductSupplierName) !== '')
                    <strong>{{ $missingProductSupplierName }}</strong>
                @endif
                <p>
                    This product does not have a supplier linked in Product Master.
                    You can cancel and link one first, or add the product without a supplier for now.
                </p>
                <small class="ft-order-product-supplier-modal-note">
                    A supplier can be assigned later from the Order Details product editor.
                </small>
            </div>
        </div>

        <x-slot:footer>
            <div class="ft-order-product-supplier-modal-actions">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    class="ft-order-product-supplier-modal-action ft-order-product-supplier-modal-action--cancel"
                    wire:click="closeMissingProductSupplierModal"
                >Cancel</x-ui.button>

                <x-ui.button
                    type="button"
                    class="ft-order-product-supplier-modal-action ft-order-product-supplier-modal-action--skip"
                    wire:click="skipMissingCreateOrderProductSupplier"
                    wire:loading.attr="disabled"
                    wire:target="skipMissingCreateOrderProductSupplier"
                >
                    <span wire:loading.remove wire:target="skipMissingCreateOrderProductSupplier">Skip supplier &amp; add product</span>
                    <span wire:loading wire:target="skipMissingCreateOrderProductSupplier">Adding…</span>
                </x-ui.button>
            </div>
        </x-slot:footer>
    </x-ui.modal>
@endif

