<?php defined('BASEPATH') or exit('No direct script access allowed');

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

require_once __DIR__ . '/../helpers/routes_helper.php';

$route['default_controller'] = 'appointments';

$route['404_override'] = '';

/*
| -------------------------------------------------------------------------
| REST API ROUTING
| -------------------------------------------------------------------------
| Define the API resource routes using the routing helper function. By 
| default, each resource will have by default the following actions: 
| 
|   - index [GET]
|
|   - show/:id [GET]
|
|   - store [POST]
|
|   - update [PUT]
|
|   - destroy [DELETE]
|
| Some resources like the availabilities and the settings do not follow this 
| pattern and are explicitly defined. 
|
*/

route_api_resource($route, 'appointments', 'api/v1/');

route_api_resource($route, 'admins', 'api/v1/');

route_api_resource($route, 'categories', 'api/v1/');

route_api_resource($route, 'customers', 'api/v1/');

route_api_resource($route, 'providers', 'api/v1/');

route_api_resource($route, 'secretaries', 'api/v1/');

route_api_resource($route, 'services', 'api/v1/');

route_api_resource($route, 'unavailabilities', 'api/v1/');

$route['api/v1/settings']['get'] = 'api/v1/settings_api_v1/index';

$route['api/v1/settings/(:any)']['get'] = 'api/v1/settings_api_v1/show/$1';

$route['api/v1/settings/(:any)']['put'] = 'api/v1/settings_api_v1/update/$1';

$route['api/v1/availabilities']['get'] = 'api/v1/availabilities_api_v1/get';

/*
| -------------------------------------------------------------------------
| CUSTOM ROUTING
| -------------------------------------------------------------------------
| You can add custom routes to the following section to define URL patterns
| that are later mapped to the available controllers in the filesystem. 
|
*/

/* End of file routes.php */
/* Location: ./application/config/routes.php */
