# Product shipment urgency extra charges

Product create/edit now uses the existing `shipment_urgency` master-data group (Shipment Urgencies), not Shipment Methods.

For products, selected urgencies are stored in `master_records.metadata.shipment_urgency_options` with:

- `shipment_urgency_id`
- `shipment_urgency_code`
- `shipment_urgency_name`
- optional `extra_charge`

The reusable Blade component is:

`resources/views/components/catalog/product-shipment-urgencies.blade.php`

No database migration is required. The earlier incorrect `shipping_options` metadata key is removed whenever a product is saved through the corrected form.

## UI behavior

- **Add shipping urgency** opens a reusable modal/card picker instead of adding an empty dropdown row.
- Active `shipment_urgency` Master Data records are shown as cards and multiple urgencies can be selected before confirming.
- Selected product urgencies are shown as compact cards on create/edit, with an optional product-specific extra charge and remove action.
- Product Details renders **Product options** and a **Shipping urgencies table** side-by-side on desktop and stacks them responsively on smaller screens.
