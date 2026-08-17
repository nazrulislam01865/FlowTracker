# FlowTrack Management Theme

The management dashboard prototype theme is reusable across FlowTrack.

## Reuse on another Blade page

Wrap the page or section with the shared component:

```blade
<x-ui.management-theme>
    <!-- page content -->
</x-ui.management-theme>
```

The component adds the `ft-management-theme` scope. Reusable design tokens and components are defined in:

- `resources/css/components/management-theme.css` — source copy
- `public/css/flowtrack-management-theme.css` — globally loaded runtime stylesheet
- `resources/views/components/ui/management-theme.blade.php` — reusable Blade wrapper

Use the `ft-mgmt-*` classes for panels, buttons, tabs, KPI cards, badges, tables, progress bars and responsive grids. The theme stays scoped so it does not overwrite unrelated pages.
