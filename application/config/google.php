<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Google Calendar - Internal Configuration
|--------------------------------------------------------------------------
|
| Declare some of the global config values of the Google Calendar
| synchronization feature.
|
| Values set here take precedence over the Google Calendar settings page, which
| then shows them as read-only. Leave them empty to configure the client in the
| admin UI at Settings > Integrations > Google Calendar.
|
*/

$config['google_sync_feature'] = defined('Config::GOOGLE_SYNC_FEATURE') ? Config::GOOGLE_SYNC_FEATURE : false;

$config['google_client_id'] = defined('Config::GOOGLE_CLIENT_ID') ? Config::GOOGLE_CLIENT_ID : '';

$config['google_client_secret'] = defined('Config::GOOGLE_CLIENT_SECRET') ? Config::GOOGLE_CLIENT_SECRET : '';
