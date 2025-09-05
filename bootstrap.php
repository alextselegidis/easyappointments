<?php

/**
 * This file is used to bootstrap the testing environment for PHPUnit.
 * It is explicitly designed for the command-line and sets up the CodeIgniter
 * framework without relying on web server variables.
 */

// Define the absolute path to your CodeIgniter installation.
// This is the core system directory where CodeIgniter lives.
define('BASEPATH', 'C:\\wamp\\www\\easyappointments\\system\\');

// The application path.
define('APPPATH', 'C:\\wamp\\www\\easyappointments\\application\\');

// The view path. This is required by the framework's core.
define('VIEWPATH', 'C:\\wamp\\www\\easyappointments\\application\\views\\');

// Set the application environment to "testing" for test-specific configurations.
define('ENVIRONMENT', 'testing');

// Load the project's own config.php file.
require_once dirname(dirname(__FILE__)) . '/config.php';

// Load the Composer autoloader.
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

// We need to disable output buffering so that the test runner can
// correctly capture the output of the tests.
ob_start();

// Include the main CodeIgniter entry point.
// We are manually including it to bypass the regular index.php flow.
require_once BASEPATH . 'core/CodeIgniter.php';

// Clear the output buffer.
ob_end_clean();
