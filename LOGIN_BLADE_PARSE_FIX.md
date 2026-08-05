# Login Blade Parse Fix

## Root cause

The login template placed Blade directives directly next to each other:

```blade
@csrf@if(...)
```

Laravel compiled `@csrf`, but the adjacent `@if` remained as literal template text. The following `@elseif` was compiled into PHP, which produced an `elseif` without a matching PHP `if` and caused the reported parse error.

## Changes

- Put `@csrf`, `@if`, `@elseif`, `@endif`, `@error`, and `@enderror` on clear, separate lines.
- Reformatted the login view so future Blade changes are safer to review.
- Made the timeout message use `config('session.lifetime')` instead of a hard-coded 30 minutes.
- Added accessible labels, autocomplete attributes, and alert roles without changing the login behavior.
- Added feature tests for the normal login page, the `other-device` message, and the `timeout` message.
- Removed generated files from `storage/framework/views`; Laravel recreates them automatically.

## Deployment cleanup

After replacing the files, run:

```bash
php artisan optimize:clear
php artisan view:cache
php artisan test --filter=AuthenticationTest
```

The first command removes any compiled copy of the broken view.
