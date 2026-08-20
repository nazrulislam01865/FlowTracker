# FlowTrack current executable state — Phase 4

This file distinguishes the current executable source from later target-state documentation.

## Current authoritative state

As of the Phase 4 snapshot:

- Laravel 13 + Livewire 4 business behavior and the existing modular-monolith deployment remain intact.
- Phase 0 architecture/quality budgets remain the regression baseline.
- Phase 1 owns static design tokens/global CSS and the legacy compatibility boundary.
- Phase 2 owns the official reusable Blade/CSS component contracts.
- Phase 3 moved direct public CSS into managed Vite source, removed application Blade style blocks, and established module/migration CSS boundaries while retaining `flowtrack.css` and the generated four-chunk compatibility mechanism.
- Phase 4 owns the shared forms/filter/search/selection interaction architecture.
- `FilterOptionService::searchPage()` plus `FilterOptionPage` are the authoritative paged selector boundary.
- Remote selector pages are capped at 20 rows; selected IDs are separately bounded/resolved; incomplete non-empty search never returns unrelated fallback rows.
- `x-ui.search-select`, `x-ui.multi-select`, `x-ui.search-input`, `x-ui.filter-bar`, `x-ui.filter-chip`, `x-ui.filter-reset`, and `x-ui.date-range` are the official feature-facing contracts.
- Existing selector components remain only as explicit compatibility adapters where a dedicated migration has not yet been visually approved.
- Document Archive, Workflow Setup client selection, Product selected-client availability, User/Admin Department selection, Inquiries, Orders and client-order filters now exercise the shared architecture.
- Phase 5 is the next backend/UI boundary: Orders/Jobs decomposition. Giant Livewire/service coordinators have not been decomposed by Phase 4.

## Quality source of truth

Run `npm run quality:phase4`. Do not re-baseline architecture, legacy CSS, selector, or test debt simply to make a regression pass.

Full PHPUnit, production Vite build and authenticated visual comparisons remain mandatory release checks in the dependency-complete development/deployment environment.
