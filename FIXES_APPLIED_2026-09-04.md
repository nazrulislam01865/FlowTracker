# FlowTrack fixes applied — 2026-09-04

## 1. Shipment -> Billing UI transition

- Kept the existing backend workflow auto-advance logic intact.
- Added a single current-workflow-stage synchronization path for the Order detail UI.
- Completing the final Shipment task now moves the visible Order task panel to the newly active Billing stage.
- Generic task status completion, task-detail completion, Save Task, manual phase completion and specialized Shipment workflow actions all synchronize the visible stage.
- A task-detail status update that advances the workflow now requests a Livewire refresh and closes the historical task detail.
- Opening another Order resets the previously selected stage, preventing a Shipment stage selection from leaking into a Billing Order.
- Historical stage viewing is still preserved when the user deliberately selects an older stage.

## 2. Unassigned workflow-task claiming

Implemented the rule:

> Assigned user can perform the task. Admin/management can override. An unassigned current-stage workflow task can be claimed by an authorized operational user.

For ordinary users, an unassigned task is claimable only when:

- it is open and belongs to the Order's current workflow stage; and
- its Task Pack routes it to the user's department, or the user is the Order owner/coordinator.

The first real task action uses the existing `TaskService::claimForAction()` behavior to:

- assign the task to the acting user;
- add the user as an Order member;
- record the assignment in activity/history.

Generic Order membership alone is deliberately **not** a claim override. This prevents a Shipment user who became an Order member from claiming a later unassigned Billing task intended for Accounts.

## 3. PNG upload mismatch shown in the supplied screenshot

- Security validation is still enforced for fake/disguised files.
- A genuine safe raster image whose filename kept the wrong raster extension (for example JPEG bytes named `*.PNG`) is now normalized to the verified image type instead of being rejected.
- The private stored file uses the verified extension and MIME type.
- PDF/document/archive mismatches and non-image content pretending to be PNG remain rejected.

## Regression coverage added

- `tests/Feature/UnassignedOrderTaskClaimAccessTest.php`
- Additional raster-extension normalization and fake-PNG rejection cases in `tests/Feature/Phase10DocumentSecurityTest.php`

## Validation performed here

- PHP syntax check passed for all 650 PHP files under `app/` and `tests/`.
- Full Laravel test execution was not available in this archive because `vendor/autoload.php` is not included and Composer is not installed in this runtime.
