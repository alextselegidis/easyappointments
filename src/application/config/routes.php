<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "appointments";
$route['404_override'] = 'errors/error404';


/*
| -------------------------------------------------------------------------
| REST API ROUTING
| -------------------------------------------------------------------------
| The following routes will point the API calls into the correct controller
| callback methods. This routes also define the HTTP verbs that they are 
| used for each operation.
|
*/

$resources = [
    'appointments',
    'unavailabilities',
    'customers', 
    'services',
    'categories',
    'admins',
    'providers', 
    'secretaries'
];

foreach($resources as $resource) {
    $route['api/v1/' . $resource]['post'] = 'api/v1/' . $resource . '/post';
    $route['api/v1/' . $resource . '/(:num)']['put'] = 'api/v1/' . $resource . '/put/$1';
    $route['api/v1/' . $resource . '/(:num)']['delete'] = 'api/v1/' . $resource . '/delete/$1';
    $route['api/v1/' . $resource]['get'] = 'api/v1/' . $resource . '/get';    
    $route['api/v1/' . $resource . '/(:num)']['get'] = 'api/v1/' . $resource . '/get/$1';
}

$route['api/v1/settings']['get'] = 'api/v1/settings/get';
$route['api/v1/settings/(:any)']['get'] = 'api/v1/settings/get/$1';
$route['api/v1/settings/(:any)']['put'] = 'api/v1/settings/put/$1';
$route['api/v1/settings/(:any)']['delete'] = 'api/v1/settings/delete/$1';

$route['api/v1/availabilities']['get'] = 'api/v1/availabilities/get';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
