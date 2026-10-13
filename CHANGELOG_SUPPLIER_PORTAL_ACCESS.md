# FlowTracker — Supplier portal access notification

## Scope

New independent **Supplier portal access email** settings card on **Administration → Reminder Setup**.

When the portal accepts account creation, or when an existing supplier's login email, username or password changes, FlowTracker queues one supplier access notification. A normal supplier edit and a repeated save with unchanged credentials do not queue an email.

The access email contains supplier name, username, login email and the public Supplier Dashboard link. **It never emails a reusable password.** When an account is created, the login email changes or a password is set, the existing signed Supplier Portal password-reset endpoint is called to send a separate one-use setup link. The supplier can also use **Forgot password** on the portal login screen.

The Reminder Setup card has an enable switch, editable subject, editable message, and Save button. Access notifications are on by default, and independently switchable. Existing workflow reminders are untouched.

## Installation

1. Back up your project, then extract the PATCH archive over the FlowTracker root (preserve the included directories).
2. Configure the public, HTTPS login link in FlowTracker's `.env`:

   `FLOWTRACK_SUPPLIER_PORTAL_LOGIN_URL=https://your-supplier-dashboard.example.com/login`

   If omitted, it falls back to `FLOWTRACK_SUPPLIER_PORTAL_URL` plus `/login`. Explicit public login URL is recommended when the signed bridge points to an internal backend address.
3. The FlowTracker email transport and supplier portal's reset-email transport must be configured. Existing signed bridge configuration `FLOWTRACK_SUPPLIER_BRIDGE_ENABLED`, `FLOWTRACK_SUPPLIER_PORTAL_URL` and `FLOWTRACK_SUPPLIER_BRIDGE_SECRET` is unchanged.
4. Run:

   ```bash
   php artisan optimize:clear
   php artisan queue:restart
   php artisan test --filter=SupplierPortalAccessReminderTest
   ```

5. Ensure your existing queue worker processes the `emails` queue (or the queue specified by `flowtrack_email.queue.name`). Example for local testing:

   ```bash
   php artisan queue:work --queue=emails,default
   ```

6. Open **Administration → Reminder Setup** and configure **Supplier portal access email**, then create a test supplier portal account.

No migration, recurring query, extra polling process or supplier portal code change is required.

## Cases

- First supplier portal account created with email/username/password: access email queued; portal sends secure one-use link.
- Existing account password changed: access email queued; one-use reset/setup link requested.
- Login email changed: new address gets a notification, and one-use link requested.
- Username changed only: access email queued; password retained without forcing a reset.
- Edit unchanged portal credentials or ordinary supplier data: no access email.
- Portal access notification disabled: no notification queued; already queued jobs check the switch again before sending.
- Deleted, inactive or subsequently changed supplier account: queued notification suppressed.
- Failed Supplier Portal provisioning: no access email.

## Delivery and resource usage

- Reuses existing `master_records` system settings (one workspace-scoped record). Settings use a five-minute cache with immediate invalidation on save.
- Reuses existing configured central `EmailService`, existing queue, and signed Supplier Portal API.
- One small queued job per access-change event; one supplier lookup when it executes; no N+1 or scheduled queries.
- Email retries happen in the background; password setup link is requested at most once per queued notification.
- Failures are logged by `flowtrack.supplier_portal.access_invitation_failed` and `flowtrack.supplier_portal.setup_link_failed` with IDs only, not passwords or plaintext email addresses.
- Only FlowTracker source is included. The Supplier Portal must already support `supplier-access/reset` (the existing manual Reset button uses it).

## Validation

PHP syntax checks and a standalone HTML-escaping smoke test passed. Full Laravel/PHPUnit tests require Composer dependencies, which were not included in the supplied archive.
