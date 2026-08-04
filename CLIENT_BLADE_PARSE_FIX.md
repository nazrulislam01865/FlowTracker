# Client Blade parse fix

Fixed the Clients page Blade parse error caused by an inline `@if / @elseif / @else / @endif` chain in the Tasks cell.

The conditional is now written as a normal multiline Blade block and the numeric counts are explicitly cast before comparison.

No database migration is required.
