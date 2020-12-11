<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use EA\Engine\Api\V1\Request;
use EA\Engine\Api\V1\Response;

/**
 * Settings Controller
 *
 * @package Controllers
 */
class Settings extends API_V1_Controller {
    /**
     * Settings Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Settings
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Settings;
    }

    /**
     * GET API Method
     *
     * @param string $name Optional (null), the setting name to be returned.
     */
    public function get($name = NULL)
    {
        try
        {
            $settings = $this->settings_model->get_settings();

            if ($name !== NULL)
            {
                $setting = NULL;

                foreach ($settings as $entry)
                {
                    if ($entry['name'] === $name)
                    {
                        $setting = $entry;
                        break;
                    }
                }

                if (empty($setting))
                {
                    $this->throw_record_not_found();
                }

                unset($setting['id']);

                $settings = [
                    $setting
                ];
            }

            $response = new Response($settings);

            $response->encode($this->parser)
                ->search()
                ->sort()
                ->paginate()
                ->minimize()
                ->singleEntry($name)
                ->output();

        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }

    /**
     * PUT API Method
     *
     * @param string $name The setting name to be inserted/updated.
     */
    public function put($name)
    {
        try
        {
            $request = new Request();
            $value = $request->get_body()['value'];
            $this->settings_model->set_setting($name, $value);

            // Fetch the updated object from the database and return it to the client.
            $response = new Response([
                [
                    'name' => $name,
                    'value' => $value
                ]
            ]);
            $response->encode($this->parser)->singleEntry($name)->output();
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }

    /**
     * DELETE API Method
     *
     * @param string $name The setting name to be deleted.
     */
    public function delete($name)
    {
        try
        {
            $this->settings_model->remove_setting($name);

            $response = new Response([
                'code' => 200,
                'message' => 'Record was deleted successfully!'
            ]);

            $response->output();
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }
}
