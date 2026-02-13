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
 * Contains all the localization related methods.
 *
 * @package Controllers
 */
class Localization extends EA_Controller
{
    /**
     * Change system language for current user.
     *
     * The language setting is stored in session data and retrieved every time the user visits any of the system pages.
    /**
     * Change the language of the user.
     */
    public function change_language(): void
    {
        try {
            method('post');

            check('language', 'string');

            $language = request('language');

            // Validate language is not empty
            if (empty($language) || !is_string($language)) {
                throw new InvalidArgumentException('Invalid language parameter.');
            }

            // Sanitize language to only allow alphanumeric and underscore characters
            $language = preg_replace('/[^a-zA-Z0-9_]/', '', $language);

            // Check if language exists in the available languages.
            if (!in_array($language, config('available_languages'), true)) {
                throw new RuntimeException('Translations for the given language does not exist.');
            }

            session(['language' => $language]);

            config(['language' => $language]);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
