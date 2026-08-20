# FlowTrack UI Design System

## Status

**Phase 2 component foundation implemented.** This document is authoritative for static design tokens, global CSS ownership, and the official reusable Blade/CSS component contracts. Phase 3 will migrate legacy page/component implementations in controlled batches.

The Phase 1 objective is architectural centralization without a visual redesign. Existing pages continue through the compatibility layer while new shared UI must use the token system.

## 1. Source ownership

```text
resources/css/
├── app.css                         # authenticated composition root only
├── foundation/
│   ├── tokens.css                  # authoritative static visual values
│   └── global.css                  # low-specificity base/accessibility/type
├── components.css                  # official component composition root
├── components/
│   ├── buttons.css
│   ├── badges.css
│   ├── forms.css
│   ├── dropdowns.css
│   ├── cards.css
│   ├── tables.css
│   ├── tabs.css
│   ├── modals.css
│   ├── tooltips.css
│   ├── pagination.css
│   ├── loading.css
│   ├── empty-state.css
│   ├── validation.css
│   └── management-theme.css        # existing compatibility CSS
├── utilities.css                   # approved narrow utilities only
├── legacy/
│   └── historical-overrides.css    # compatibility boundary -> flowtrack.css
├── flowtrack.css                   # frozen legacy stylesheet, shrinking only
└── generated/
    ├── flowtrack-01.css
    ├── flowtrack-02.css
    ├── flowtrack-03.css
    └── flowtrack-04.css
```

`app.css` is the source composition root. During the compatibility period, `scripts/split-flowtrack-css.mjs` expands its local import graph and regenerates the four entries already loaded by the authenticated layout. Removing this compatibility delivery path remains a Phase 3 task.

## 2. Token naming contract

All new static design tokens use `--ft-<category>-<role-or-scale>`.

Examples: `--ft-color-primary-600`, `--ft-bg-surface`, `--ft-text-secondary`, `--ft-border-default`, `--ft-font-size-md`, `--ft-space-4`, `--ft-radius-md`, `--ft-shadow-lg`, `--ft-duration-normal`, `--ft-z-modal`.

Raw static design values belong in `foundation/tokens.css` only. New shared/module CSS consumes tokens rather than duplicating color, shadow, spacing, typography, radius, or transition values.

## 3. Token model

The token file owns primary blue, teal/accent, purple, success, warning, danger, and neutral scales plus semantic aliases for page/surface/text/border roles.

Prefer semantic aliases when meaning is known:

| Role | Token |
|---|---|
| Page background | `--ft-bg-page` |
| Surface | `--ft-bg-surface` |
| Subtle surface | `--ft-bg-surface-subtle` |
| Primary text | `--ft-text-primary` |
| Secondary text | `--ft-text-secondary` |
| Link | `--ft-text-link` |
| Default border | `--ft-border-default` |
| Strong border | `--ft-border-strong` |
| Focus border | `--ft-border-focus` |
| Brand primary | `--ft-color-brand-primary` |
| Primary hover | `--ft-color-brand-primary-hover` |

## 4. Typography

The controlled contract contains a font-family token, sizes `xs` through `4xl`, regular/medium/semibold/strong/bold/extrabold weights, line-height roles, and letter-spacing roles.

Phase 1 provides opt-in semantic type roles in `foundation/global.css`: `.ft-type-page-title`, `.ft-type-section-title`, `.ft-type-subsection-title`, `.ft-type-body`, `.ft-type-label`, `.ft-type-caption`, `.ft-type-helper`, and `.ft-link`.

## 5. Spacing, borders, radius and elevation

Use the approved `--ft-space-*` scale in shared CSS. Borders use centralized width/style/color roles. Radius uses `--ft-radius-*`; elevation uses `--ft-shadow-*`. Shared components must not invent local shadow/radius values.

## 6. Motion and accessibility

Global foundation behavior includes inherited form-control typography, keyboard `:focus-visible`, reduced-motion behavior, disabled/ARIA-disabled cursor semantics, and low-specificity rules inside the `ft-foundation` cascade layer.

Legacy CSS remains unlayered and loads after the foundation, preserving approved current-page visuals during migration.

## 7. Runtime Master Data colors

Workflow phases, statuses, priorities, flags and departments are runtime data, not static tokens. The approved migration contract is a validated runtime CSS custom property passed to a shared component. Static page/component design values must not use that exception.

## 8. Legacy compatibility policy

Compatibility debt includes `flowtrack.css`, login CSS until migrated, existing management-theme CSS until normalized, existing direct `public/css/flowtrack-*.css`, and the generated four-chunk delivery mechanism.

Rules:

1. No new feature CSS in `flowtrack.css`.
2. No new direct stylesheet under `public/css`.
3. Existing legacy bytes, `!important`, and hard-coded color counts may only decrease.
4. `legacy/historical-overrides.css` is a boundary, not a dumping ground.
5. Generated CSS is a build artifact and must not be hand-edited.
6. New global classes use `ft-`; approved narrow utilities use `u-`.
7. Generic global classes such as `.primary`, `.secondary`, `.card`, `.field`, `.toolbar`, or `.badge` are prohibited in new architecture CSS.
8. New `!important` is prohibited in managed foundation/shared CSS.

These rules are enforced by `php scripts/quality/css-foundation-budget.php`.

## 9. Phase 1 compatibility aliases

The token root temporarily exposes historical variables consumed by existing `flowtrack.css` selectors (`--blue`, `--bg`, `--line`, etc.). They are aliases only and contain no independent static design values. New CSS must never use them.

## 10. Required import order

```css
@import './foundation/tokens.css';
@import './foundation/global.css';
@import './components.css';
@import './utilities.css';
@import './legacy/historical-overrides.css';
```

Do not reorder this during Phase 2. The component library must stay before the legacy boundary so existing legacy rules remain the compatibility fallback until Phase 3.

## 11. Preferred usage

```css
.ft-example-panel {
    padding: var(--ft-space-4);
    border: var(--ft-border-width-thin) var(--ft-border-style-default) var(--ft-border-default);
    border-radius: var(--ft-radius-xl);
    background: var(--ft-bg-surface);
    color: var(--ft-text-primary);
    box-shadow: var(--ft-shadow-sm);
}
```

## 12. Quality commands

```bash
npm run css:split
npm run quality:architecture
npm run quality:css
npm run quality:phase1
npm run quality:components
npm run quality:phase2
npm run build
```

`quality:phase1` regenerates compatibility chunks and runs both the Phase 0 architecture ceiling and Phase 1 CSS ownership/freeze gate.

## 13. Change process

A new token requires a reusable semantic purpose, reuse of an existing equivalent token when possible, placement in the correct token group, documentation for a new category/contract, a passing Phase 1 gate, and visual comparison when an existing selector starts consuming it.

Phase 2 now consumes this foundation for the official buttons, badges, forms, dropdowns, cards, tables, tabs, modals, pagination, loading, empty-state, and validation contracts documented in `docs/ui-components.md`.


## 14. Phase 2 official component library

Phase 2 adds the official component contract documented in `docs/ui-components.md`. Component CSS is token-driven, contains no `!important`, and is isolated from legacy class-name collisions with `data-ft-ui-component` markers. New feature work should use `x-ui.*` components rather than writing page-level button, badge, card, field, table, tabs, modal, tooltip, pagination, loading, empty-state, or validation implementations.

`App\Support\MasterColor::style()` emits the official `--ft-dynamic-*` runtime-color variables while retaining the historical `--ft-master-*` bridge. This keeps Master Data color semantics compatible during incremental migration.


## Phase 3 migration boundary

Phase 3 introduced `resources/css/migration/` for extracted legacy component styles and `resources/css/modules/` for feature/page layout ownership. These are transitional source locations with explicit debt ceilings. New feature styling must not copy their legacy hard-coded values; new work continues to consume Phase 1 tokens and Phase 2 official components. Runtime Master Data colors and data-driven geometry remain the approved inline custom-property/geometry exception.

## Phase 4 interaction-system ownership

Searchable selects, multi-selects, filter bars, search fields, filter chips, reset actions, date ranges and their validation/loading/error states now have one shared visual and behavioral contract. Feature/module CSS may control placement only. It must not create another selector/dropdown visual implementation.

Large selector datasets are server-side, permission-scoped and bounded by `FilterOptionService::searchPage()`. Runtime query results are data, not design tokens. No-result and incomplete-search states must stay empty rather than returning arbitrary fallback options.
