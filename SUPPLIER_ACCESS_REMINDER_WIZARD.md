# FlowTracker: Supplier Portal Access in Create Reminder

## What changed

Supplier Portal Access email is managed through the same **Reminder Setup > Create Reminder** wizard used for Order reminders. The standalone Supplier Portal Access settings block was removed.

- Click **Create Reminder**, select **Module: Supplier**, **Portal Access** stage and **Dashboard account created or credentials changed** event.
- Follow the same Trigger & Recipients, Email Content, Preview & Test, and Save & Publish steps as Order reminders.
- Once published, the saved supplier portal login email receives the email when a supplier portal account is successfully created, or its login email, username or password changes. Ordinary unchanged saves do not send new emails.
- The message includes the portal login email, username and dashboard URL. No password is emailed: the existing secure password-reset service supplies a one-use setup link when needed.
- Existing published Order reminders retain their workflow trigger and rendering behavior.
- Pausing the Supplier Portal Access reminder stops new sends and skips delayed jobs that have not been processed.

## Migration / compatibility

Run `php artisan migrate` once. The migration copies the previously saved Supplier Portal Access settings (if present) into a normal reminder entry and preserves its enabled/disabled status. It leaves the old setting record untouched for rollback safety. If there were no saved settings, create and publish the new Supplier Portal Access reminder manually.

The supplier event uses the existing indexed reminder settings table and shared cached published-template read. **No additional runtime database tables or periodic scans are introduced.** The new migration performs a one-time read/insert for existing settings only.

The existing order Reminder Delivery History table records order task deliveries; supplier portal emails continue to be audited in centralized application email logs, without adding a second delivery table.

## Installation

1. Backup the code and database, then extract the patch into the FlowTracker project root, preserving the directory tree.
2. Configure the public login URL in FlowTracker `.env`:

   ```dotenv
   FLOWTRACK_SUPPLIER_PORTAL_LOGIN_URL=https://8.163.67.79/supplier/login
   ```

3. Run:

   ```bash
   php artisan migrate
   php artisan optimize:clear
   php artisan queue:restart
   php artisan test --filter=SupplierPortalAccessReminderTest
   php artisan test --filter=OrderReminderEventConfigurationTest
   ```

4. Keep your existing `emails` queue worker and outgoing email provider running.
5. Open Reminder Setup and confirm that the migrated Supplier Portal Access entry is present. Configure its email and test with an address you control. The test email does not provision accounts or issue password reset tokens.
6. Check the public HTTPS certificate for the configured IP address to avoid browser warnings.

## Tests performed here

PHP lint for changed classes, job, migration and tests; JavaScript syntax; a standalone HTML-escaping + template-variable smoke test; and static page bindings. Full Laravel integration tests and SMTP sending could not be executed because `vendor/` and a configured database/mail transport were not included in the source archive.
