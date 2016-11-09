<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use \EA\Engine\Api\V1\Response;
use \EA\Engine\Api\V1\Request;
use \EA\Engine\Types\NonEmptyText;

/**
 * Providers Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Providers extends API_V1_Controller {
    /**
     * Providers Resource Parser
     * 
     * @var \EA\Engine\Api\V1\Parsers\Providers
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('providers_model'); 
        $this->parser = new \EA\Engine\Api\V1\Parsers\Providers;
    }

    /**
     * GET API Method 
     * 
     * @param int $id Optional (null), the record ID to be returned. 
     */
    public function get($id = null) {
        try {
            $condition = $id !== null ? 'id = ' . $id : null; 
            $providers = $this->providers_model->get_batch($condition); 

            if ($id !== null && count($providers) === 0) {
                $this->_throwRecordNotFound();
            }

            $response = new Response($providers);

            $response->encode($this->parser)
                    ->search()
                    ->sort()
                    ->paginate()
                    ->minimize()
                    ->singleEntry($id)
                    ->output();

        } catch (\Exception $exception) {
            $this->_handleException($exception);
        }
    }

    /**
     * POST API Method 
     */
    public function post() {
        try {
            // Insert the provider to the database. 
            $request = new Request(); 
            $provider = $request->getBody(); 
            $this->parser->decode($provider); 
            
            if (isset($provider['id'])) {
                unset($provider['id']);
            }

            $id = $this->providers_model->add($provider);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->providers_model->get_batch('id = ' . $id); 
            $response = new Response($batch); 
            $status = new NonEmptyText('201 Created');
            $response->encode($this->parser)->singleEntry(true)->output($status);
        } catch (\Exception $exception) {
            $this->_handleException($exception);
        }
    }

    /**
     * PUT API Method 
     *
     * @param int $id The record ID to be updated. 
     */
    public function put($id) {
        try {
            // Update the provider record. 
            $batch = $this->providers_model->get_batch('id = ' . $id); 

            if ($id !== null && count($batch) === 0) {
                $this->_throwRecordNotFound();
            }
            
            $request = new Request(); 
            $updatedProvider = $request->getBody(); 
            $baseProvider = $batch[0];
            $this->parser->decode($updatedProvider, $baseProvider); 
            $updatedProvider['id'] = $id; 
            $id = $this->providers_model->add($updatedProvider);
            
            // Fetch the updated object from the database and return it to the client.
            $batch = $this->providers_model->get_batch('id = ' . $id); 
            $response = new Response($batch); 
            $response->encode($this->parser)->singleEntry($id)->output(); 
        } catch (\Exception $exception) {
            $this->_handleException($exception);
        }
    }

    /**
     * DELETE API Method 
     *
     * @param int $id The record ID to be deleted. 
     */
    public function delete($id) {
        try {
            $result = $this->providers_model->delete($id);

            $response = new Response([
                'code' => 200, 
                'message' => 'Record was deleted successfully!'
            ]);

            $response->output();
        } catch (\Exception $exception) {
            $this->_handleException($exception);
        }
    }
}

/* End of file Providers.php */
/* Location: ./application/controllers/api/v1/Providers.php */
