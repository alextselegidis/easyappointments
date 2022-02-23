<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.3
 * ---------------------------------------------------------------------------- */

/**
 * Localization Controller
 *
 * Contains all the location related methods.
 *
 * @package Controllers
 */
class Localization extends EA_Controller {
    /**
     * Change system language for current user.
     *
     * The language setting is stored in session data and retrieved every time the user visits any of the system pages.
     *
     * Notice: This method used to be in the Backend_api.php.
     */
    public function ajax_change_language()
    {
        try
        {
            // Check if language exists in the available languages.
            $found = FALSE;

            $language = $this->input->post('language');

            if (empty($language))
            {
                throw new Exception('No language provided.');
            }

            foreach (config('available_languages') as $available_language)
            {
                if ($available_language === $language)
                {
                    $found = TRUE;
                    break;
                }
            }

            if ( ! $found)
            {
                throw new Exception('The translations for the provided language do not exist: ' . $language);
            }

            $this->session->set_userdata('language', $language);

            $this->config->set_item('language', $language);

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
