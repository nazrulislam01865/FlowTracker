# Job Overview task-status / automatic phase-advance fix

Fixed an exception raised after changing a task status from Job Details > Overview.

## Root cause
`abort_if()` evaluates all function arguments before it decides whether to abort. The previous code built the abort message with `$blockers->first()->description` even when the blocker collection was empty. That caused `Attempt to read property \"description\" on null` exactly when the phase was ready to auto-advance.

## Fix
The service now checks `isNotEmpty()` first and only reads the first blocker inside that branch. The blocker message is also null-safe and has a fallback message.

No database migration is required.
