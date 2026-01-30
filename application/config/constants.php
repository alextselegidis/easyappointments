<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
const FILE_READ_MODE = 0644;
const FILE_WRITE_MODE = 0666;
const DIR_READ_MODE = 0755;
const DIR_WRITE_MODE = 0777;

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

const FOPEN_READ = 'rb';
const FOPEN_READ_WRITE = 'r+b';
const FOPEN_WRITE_CREATE_DESTRUCTIVE = 'wb'; // truncates existing file data, use with care
const FOPEN_READ_WRITE_CREATE_DESTRUCTIVE = 'w+b'; // truncates existing file data, use with care
const FOPEN_WRITE_CREATE = 'ab';
const FOPEN_READ_WRITE_CREATE = 'a+b';
const FOPEN_WRITE_CREATE_STRICT = 'xb';
const FOPEN_READ_WRITE_CREATE_STRICT = 'x+b';

/*
|--------------------------------------------------------------------------
| Application Data
|--------------------------------------------------------------------------
|
| These constants are used globally from the application when handling data.
|
*/
const DB_SLUG_CUSTOMER = 'customer';
const DB_SLUG_PROVIDER = 'provider';
const DB_SLUG_ADMIN = 'admin';
const DB_SLUG_SECRETARY = 'secretary';

const FILTER_TYPE_ALL = 'all';
const FILTER_TYPE_PROVIDER = 'provider';
const FILTER_TYPE_SERVICE = 'service';

const AJAX_SUCCESS = 'SUCCESS';
const AJAX_FAILURE = 'FAILURE';

const SETTINGS_SYSTEM = 'SETTINGS_SYSTEM';
const SETTINGS_USER = 'SETTINGS_USER';

const PRIV_VIEW = 1;
const PRIV_ADD = 2;
const PRIV_EDIT = 4;
const PRIV_DELETE = 8;

const PRIV_APPOINTMENTS = 'appointments';
const PRIV_CUSTOMERS = 'customers';
const PRIV_SERVICES = 'services';
const PRIV_USERS = 'users';
const PRIV_SYSTEM_SETTINGS = 'system_settings';
const PRIV_USER_SETTINGS = 'user_settings';
const PRIV_WEBHOOKS = 'webhooks';
const PRIV_BLOCKED_PERIODS = 'blocked_periods';

const DATE_FORMAT_DMY = 'DMY';
const DATE_FORMAT_MDY = 'MDY';
const DATE_FORMAT_YMD = 'YMD';

const TIME_FORMAT_REGULAR = 'regular';
const TIME_FORMAT_MILITARY = 'military';

const MIN_PASSWORD_LENGTH = 7;
const MAX_PASSWORD_LENGTH = 100;
const ANY_PROVIDER = 'any-provider';

const CALENDAR_VIEW_DEFAULT = 'default';
const CALENDAR_VIEW_TABLE = 'table';

const AVAILABILITIES_TYPE_FLEXIBLE = 'flexible';
const AVAILABILITIES_TYPE_FIXED = 'fixed';

const EVENT_MINIMUM_DURATION = 5; // Minutes

const DEFAULT_COMPANY_COLOR = '#ffffff';

const LDAP_DEFAULT_FILTER = '(&(objectClass=*)(|(cn={{KEYWORD}})(sn={{KEYWORD}})(mail={{KEYWORD}})(givenName={{KEYWORD}})(uid={{KEYWORD}})))';

const LDAP_WHITELISTED_ATTRIBUTES = [
    'givenname',
    'cn',
    'dn',
    'sn',
    'mail',
    'telephonenumber',
    'description',
    'member',
    'objectclass',
    'objectcategory',
    'instancetype',
    'whencreated',
    'name',
    'samaccountname',
    'samaccounttype',
    'objectcategory',
    'memberof',
    'distinguishedname',
];

const LDAP_DEFAULT_FIELD_MAPPING = [
    'first_name' => 'givenname',
    'last_name' => 'sn',
    'email' => 'mail',
    'phone_number' => 'telephonenumber',
    'username' => 'cn',
];

/*
|--------------------------------------------------------------------------
| Webhook Actions
|--------------------------------------------------------------------------
|
| External application endpoints can subscribe to these webhook actions.  
|
*/

const WEBHOOK_APPOINTMENT_SAVE = 'appointment_save';
const WEBHOOK_APPOINTMENT_DELETE = 'appointment_delete';
const WEBHOOK_UNAVAILABILITY_SAVE = 'unavailability_save';
const WEBHOOK_UNAVAILABILITY_DELETE = 'unavailability_delete';
const WEBHOOK_CUSTOMER_SAVE = 'customer_save';
const WEBHOOK_CUSTOMER_DELETE = 'customer_delete';
const WEBHOOK_SERVICE_SAVE = 'service_save';
const WEBHOOK_SERVICE_DELETE = 'service_delete';
const WEBHOOK_SERVICE_CATEGORY_SAVE = 'service_category_save';
const WEBHOOK_SERVICE_CATEGORY_DELETE = 'service_category_delete';
const WEBHOOK_PROVIDER_SAVE = 'provider_save';
const WEBHOOK_PROVIDER_DELETE = 'provider_delete';
const WEBHOOK_SECRETARY_SAVE = 'secretary_save';
const WEBHOOK_SECRETARY_DELETE = 'secretary_delete';
const WEBHOOK_ADMIN_SAVE = 'admin_save';
const WEBHOOK_ADMIN_DELETE = 'admin_delete';
const WEBHOOK_BLOCKED_PERIOD_SAVE = 'blocked_period_save';
const WEBHOOK_BLOCKED_PERIOD_DELETE = 'blocked_period_delete';

/* End of file constants.php */
/* Location: ./application/config/constants.php */
