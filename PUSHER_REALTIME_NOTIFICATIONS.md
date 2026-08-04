# FlowTrack Pusher realtime notifications

FlowTrack now stores every notification in `flow_notifications` first and then publishes the same notification to a private per-user Pusher channel. If Pusher is unavailable, Job/Task updates still complete and the notification remains available on the Notifications page.

Add these values to the deployment `.env` using the credentials from the Pusher Channels app:

```env
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=
PUSHER_SCHEME=https
# Optional self-hosted/compatible endpoint overrides:
# PUSHER_HOST=
# PUSHER_PORT=443
```

Then run:

```bash
php artisan migrate
php artisan optimize:clear
npm run build
```

No Pusher PHP Composer package or npm package is required by this implementation. The browser uses Pusher JS 8.4 from Pusher's official CDN, while the Laravel application signs private-channel authentication and Pusher REST events directly.

Notification delivery follows the Role Matrix. The recipient must be active, have `notifications.view`, and have record-level access to the Job/Task. Task notifications remain assignee-strict for Assigned Jobs roles. Administrators receive management-attention/risk alerts and retain unrestricted access.
