<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

use Gregwar\Captcha\CaptchaBuilder;

/**
 * Captcha controller.
 * 
 * Handles the captcha operations.
 *
 * @package Controllers
 */
class Captcha extends EA_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('captcha_builder');
    }

    /**
     * Make a request to this method to get a captcha image.
     */
    public function index()
    {
        $builder = new CaptchaBuilder;
        $builder->setDistortion(false);
        $builder->setMaxBehindLines(1);
        $builder->setMaxFrontLines(1);
        $builder->setBackgroundColor(255,255,255);
        $builder->build();
        session(['captcha_phrase' => $builder->getPhrase()]);
        header('Content-type: image/jpeg');
        $builder->output();
    }
}
