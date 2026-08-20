# FlowTrack refactor engineering governance

## Purpose

Phase 0 freezes observable behavior and technical-debt ceilings before structural refactoring starts. The rule is simple: later phases may reduce debt, but they must not silently increase it while migration is in progress.

## Non-negotiable refactor rules

1. **Preserve behavior first.** Structural work does not change business semantics unless the change is explicitly approved and separately tested.
2. **Keep every batch deployable.** No half-migrated feature may require old and new implementations to disagree about a rule.
3. **Do not redesign during structural migration.** Visual changes require explicit approval and updated visual baselines.
4. **Architecture debt is a ceiling.** `php scripts/quality/architecture-budget.php --check` must pass. Any temporary exception requires a written reason, owner, expiry phase, and follow-up issue.
5. **Sensitive operations remain server-authorized.** Blade visibility is never treated as authorization.
6. **Reads stay bounded.** New list/search/report work must deliberately choose pagination, aggregation, or a justified bounded read.
7. **Compatibility code is temporary.** An adapter must name its replacement boundary and deletion condition.
8. **No Phase 1+ structure is claimed before it exists.** Documentation must distinguish current executable state from target state.

## Phase 0 architecture debt budgets

The authoritative values are stored in `quality/architecture-baseline.json`. The scanner currently covers:

- line counts for the six largest coordinators/services identified by the roadmap;
- Blade `@php`, `app()`, `auth()`, `style=`, `<style>` and hard-coded hex usage;
- canonical CSS `!important` and hard-coded hex usage;
- `flowtrack.css` bytes, lines, `!important` and color occurrence counts;
- models using `protected $guarded = []`;
- application `->get()` call count as an early bounded-read debt signal.

File-count metrics are recorded for context but are not treated as debt ceilings because modularization can legitimately increase the number of focused files.

## Pull request release gate

Every refactor pull request must state:

- phase and migration batch;
- affected workflows and routes;
- functional tests executed and any known pre-existing failures;
- visual scenarios checked at desktop/tablet/mobile where presentation changes;
- architecture-budget result;
- before/after performance evidence when queries/data loading change;
- authorization denial tests for sensitive read/write changes;
- rollback unit (normally one migration batch/commit set).

A batch is not complete merely because the new implementation works. The obsolete implementation should be deleted when call-site search and regression coverage prove it is no longer needed.

## Exception process

A quality-gate exception is allowed only to unblock a production defect. Record:

- exact failing metric/gate;
- why fixing it in the same change is riskier;
- owner;
- expiry date or refactor phase;
- maximum permitted temporary delta;
- rollback/follow-up reference.

Never regenerate `quality/architecture-baseline.json` simply to make a regression pass. Re-baselining is appropriate only after an approved debt reduction or an explicitly accepted scope change.
