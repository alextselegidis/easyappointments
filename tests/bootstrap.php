<?php
/**
 * PHPUnit Bootstrap File
 *
 * Loads required components for testing without the full CodeIgniter framework.
 */

// Define BASEPATH to satisfy helper file guards
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load application helpers needed for tests
require_once APPPATH . 'helpers/array_helper.php';
require_once APPPATH . 'helpers/validation_helper.php';

