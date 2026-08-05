# Progressive Create Job, Board and My Work rendering

## Create Job

- Job basics and visible clients render first.
- categories load when the Products section approaches the viewport; product options wait until a category is selected.
- users and priority options load when Schedule & Owner approaches the viewport.
- workflows, starting phases and Task Pack template counts load when Workflow approaches the viewport.
- fixed-size skeleton sections preserve the form layout during each request.
- Save and Create remain disabled until all required sections are ready.
- none of these datasets are loaded or rendered on the Jobs list.

## Job Board and Task Board

- page heading, tabs, filters, quick counts and lane definitions render first.
- the expensive card queries and related models load in one `wire:init` request.
- Job Board and Task Board remain separate query branches.
- switching modes or applying filters loads the requested cards directly, avoiding a second wait.

## My Work

- heading, filters, counts and status lanes render first.
- task cards and their Job, client, phase, assignee and count relationships load in one follow-up request.
- later filters and pagination render directly after initialization.

Responsive skeletons reserve the final content area, and reduced-motion preferences disable their animation. No database migration is required.
