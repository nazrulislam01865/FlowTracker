# Product option extra charge

Product options now support an optional numeric `extra_charge` stored with each option in product metadata.

- Existing options without a charge load as `0.00`.
- The create/edit Product form shows Label, Extra charge, Image and Remove on the same compact row.
- Product Details shows the saved extra charge next to each option.
- `MasterRecord::productOptionExtraCharge()` retrieves a single option charge.
- `MasterRecord::productOptionsExtraCharge()` sums multiple selected option charges.
- `MasterRecord::productPriceForQuantityWithOptions()` returns the quantity-based base unit price plus selected option charges.

The extra charge is treated as a per-unit addition to the product price. No database migration is required because product options are stored in existing product metadata.
