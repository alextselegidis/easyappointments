<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| TESTING ROUTES
| -------------------------------------------------------------------------
| The following routes are defined in order for CI to be able to process 
| test execution requests via the CLI. 
|
| The Test controller class is used as a placeholder for this purpose.
|
*/

$route['default_controller'] = 'test/index';
$route['404_override'] = 'test/index'; // when in doubt, use the hammer
$route['translate_uri_dashes'] = FALSE;

/* End of file routes.php */
/* Location: ./application/config/testing/routes.php */
