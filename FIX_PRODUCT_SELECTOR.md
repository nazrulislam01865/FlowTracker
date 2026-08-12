# Create Order product selector fix

The Create Order product selector now uses the exact same canonical Master Data Products source as the Product List page (`master_records` records whose `type` is `product`).

Permanent safeguards:
- Product Category records cannot be returned as search results.
- The text search matches Product name and Product code only.
- Product Category is used only by the separate category filter.
- Search result cards show Product name and Product code, not Product Category metadata.
- Small product catalogues show all matching Product records immediately; larger catalogues keep the Top matches / View all results behaviour.
- Selection still resolves the selected ID through `ProductCatalogService::findActiveProductOrFail()`, so a category ID cannot be selected as a Product.
