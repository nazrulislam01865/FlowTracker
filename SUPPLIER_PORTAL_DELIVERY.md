# Supplier Portal addition

New standalone service: `supplier-portal/`.

The existing FlowTracker `app/`, `routes/`, `config/`, migrations and public assets are deliberately unchanged. Do not merge supplier controllers/migrations into the original app. See `supplier-portal/README.md` for configuration, mandatory process/database isolation, explicit local demo setup, and the future API integration boundary.
