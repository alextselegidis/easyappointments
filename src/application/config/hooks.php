<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
// This hook is necessary to make the phpunit work with 
// the codeigniter framework.
$hook['display_override'] = array(
	'class' => 'DisplayHook',
	'function' => 'captureOutput',
	'filename' => 'DisplayHook.php',
	'filepath' => 'hooks'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */