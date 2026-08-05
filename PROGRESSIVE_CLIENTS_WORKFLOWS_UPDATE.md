# Progressive Clients and Workflows update

## Clients

- The first response renders the page shell, metrics, filters, and table placeholders.
- Paginated client rows, summary metrics, and filter choices load after the shell.
- Add Client is a branch-specific render: it does not query the client list, summary, filters, preview, or detail data.
- Account-manager choices load only when the Add Client form is visible.
- Client detail data loads only after a client is opened.
- Detail lists are capped at the 50 most recent active Jobs; counts and health still cover the full accessible dataset.
- Summary metrics are cached for 30 seconds and filter choices for five minutes per user.

## Workflows

- Workflow Setup renders stable placeholders before requesting its data.
- The template sidebar uses a lightweight catalog with `phases_count` instead of loading every phase for every template.
- Full phase relations load only for the selected workflow.
- Task Pack and document-category choices query only while the Add/Edit Phase modal is open.
- Create Workflow loads the lightweight duplication choices after the form shell; Edit Workflow does not query them.
- Workflow and phase actions validate that records belong to the current workspace/selected workflow.

## Scaling and responsive behavior

- Large client and workflow tables use bounded scroll areas and sticky table headers.
- Workflow template lists scroll independently on desktop and remain horizontally navigable on smaller screens.
- Skeletons preserve layout to reduce visual movement while data arrives.
- Loading actions disable repeated submissions.
- Reduced-motion preferences disable skeleton animation.

No database migration is required.
