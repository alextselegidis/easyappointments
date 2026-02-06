<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

/**
 * Add security headers to all responses.
 */
$hook['post_controller_constructor'][] = [
    'class' => '',
    'function' => 'add_security_headers',
    'filename' => 'security_headers.php',
    'filepath' => 'hooks',
    'params' => [],
];

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
