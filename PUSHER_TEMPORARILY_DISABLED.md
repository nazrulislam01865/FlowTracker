# Pusher temporarily disabled

Pusher has been disabled with a server-side kill switch in `PusherChannelService::enabled()` and in `config/services.php`.

Database notifications and fallback unread-count polling remain active. No Pusher script, channel authentication, realtime queue job, or outbound Pusher request will run while the kill switch is disabled.
