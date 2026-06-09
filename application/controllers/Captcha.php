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
class Captcha extends EA_Controller
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Make a page request to this method in order to output a fresh captcha image.
     */
    public function index(): void
    {
        method('get');

        $captcha_builder = new CaptchaBuilder();

        $captcha_builder->setDistortion(true);
        $captcha_builder->setMaxBehindLines(1);
        $captcha_builder->setMaxFrontLines(1);
        $captcha_builder->setBackgroundColor(255, 255, 255);
        $captcha_builder->build();
        session(['captcha_phrase' => $captcha_builder->getPhrase()]);
        header('Content-type: image/jpeg');
        $captcha_builder->output();
    }

    /**
     * Generate an ALTCHA challenge.
     */
    public function altcha_challenge(): void
    {
        try {
            method('get');

            $this->load->library('altcha_client');

            if (!$this->altcha_client->is_enabled()) {
                json_response([
                    'error' => 'ALTCHA is not enabled',
                ], 400);
                return;
            }

            $challenge = $this->altcha_client->create_challenge();

            json_response($challenge);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
