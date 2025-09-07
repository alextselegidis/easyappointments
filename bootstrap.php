<?php


define('BASEPATH', 'C:\\wamp\\www\\easyappointments\\system\\');

define('APPPATH', 'C:\\wamp\\www\\easyappointments\\application\\');

define('VIEWPATH', 'C:\\wamp\\www\\easyappointments\\application\\views\\');

define('ENVIRONMENT', 'testing');

require_once dirname(dirname(__FILE__)) . '/config.php';

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

ob_start();

require_once BASEPATH . 'core/CodeIgniter.php';

ob_end_clean();
