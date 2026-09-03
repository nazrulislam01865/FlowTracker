# Remote Area Master Data — 2026-09-03

## Scope

Added a new **Remote Areas** option under the existing Master Data administration section. It uses the existing `master_records` architecture, so no schema migration is required.

Each Remote Area record stores:

- Remote area name (`master_records.name`)
- Postal code (`metadata.postal_code`)
- Optional extra charge (`metadata.extra_charge`)
- Notes/description, status and sort order using the existing Master Data fields

## Behavior

- New records receive automatic `RMA-###` codes.
- Postal codes are trimmed, uppercased and internal whitespace is normalized.
- Postal codes are unique per workspace within Remote Areas to avoid ambiguous surcharge matches.
- Extra charge is optional and validated as a non-negative amount up to `999999.99`.
- The Remote Areas list can be searched by area name, code, notes or postal code.
- Active records can be resolved later through `MasterDataService::remoteAreaForPostalCode()` when order/shipping surcharge automation is added.

## UI

The generic Master Data editor now shows Remote Area, Postal Code, optional Extra Charge, Notes, Status and Sort Order for this group. The list adds dedicated Postal Code and Extra Charge columns.
