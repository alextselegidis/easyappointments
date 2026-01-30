<?php

namespace Tests;

use CI_Controller;
use EA_Controller;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Parent test case sharing common test functionality.
 */
class TestCase extends PHPUnitTestCase {
    /**
     * @var EA_Controller|CI_Controller
     */
    private static EA_Controller|CI_Controller $CI;

    /**
     * Load the framework instance.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$CI =& get_instance();
    }
}
