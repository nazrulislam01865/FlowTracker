# FlowTrack current executable state — Phase 0

This file distinguishes the **current source tree** from target-state documentation that already exists in the repository.

## Current authoritative state

As of the Phase 0 baseline snapshot:

- the application is a Laravel 13 / Livewire 4 modular monolith in intent, but the executable source still uses the existing `app/Livewire`, `app/Services`, `resources/css/flowtrack.css`, generated CSS chunks and current Blade structure;
- `app/Features`, `app/Actions`, `app/Queries`, `resources/css/foundation`, `resources/css/modules`, `resources/css/pages` and `resources/css/legacy` are **not yet present** in this source snapshot;
- Phase 1+ target directories must not be claimed as implemented until the corresponding phase creates and verifies them;
- current behavior, not target documentation, is the source of truth for regression baselines.

## Target-state documentation

Existing documents such as `docs/ARCHITECTURE.md`, `docs/DEVELOPER_GUIDE.md`, `docs/CSS_PHASE_2_REPORT.md` and `docs/REFACTORING_REPORT.md` contain future/refactored structures that are useful as design direction but are ahead of this executable snapshot.

During the staged refactor:

1. preserve those documents as intended direction;
2. use `docs/refactor/PHASE_0_BASELINE.md` and `quality/architecture-baseline.json` for the current objective baseline;
3. update target documentation only when the corresponding source structure actually exists;
4. never regenerate budgets simply to hide a regression.
