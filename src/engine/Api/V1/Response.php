<?php 

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

namespace EA\Engine\Api\V1; 

use EA\Engine\Types\NonEmptyText;

/**
 * API v1 Response 
 *
 * Use chain-calls of the class methods to easily manipulate the provided response array. This class will
 * use directly the provided GET parameters for easier manipulation.
 *
 * Example:
 *   $parser = new \EA\Engine\Api\V1\Parsers\Appointments;
 *   $response = new \EA\Engine\Api\V1\Response($data);
 *   $response->format($parser)->search()->sort()->paginate()->minimize()->output();
 */
class Response {
    /**
     * Contains the response information. 
     * 
     * @var array
     */
    protected $response;

    /**
     * Class Constructor 
     * 
     * @param array $response Provide unfiltered data to be processed as an array. 
     */
    public function __construct(array $response) {
        $this->response = $response;
    }

    /**
     * Encode the response entries to the API compatible structure. 
     * 
     * @param Parsers\ParsersInterface $parser Provide the corresponding parser class.
     *
     * @return \EA\Engine\Api\V1\Response
     */
    public function encode(Parsers\ParsersInterface $parser) {
        foreach ($this->response as &$entry) {
            $parser->encode($entry);
        }

        return $this;
    }

    /**
     * Perform a response search. 
     * 
     * @return \EA\Engine\Api\V1\Response
     */
    public function search() {
        Processors\Search::process($this->response); 

        return $this;
    }

    /**
     * Perform a response sort. 
     *
     * @return \EA\Engine\Api\V1\Response
     */
    public function sort() {
        Processors\Sort::process($this->response); 

        return $this;
    }

    /**
     * Perform a response paginate. 
     *
     * @return \EA\Engine\Api\V1\Response
     */
    public function paginate() {
        Processors\Paginate::process($this->response); 

        return $this;
    }

    /**
     * Perform a response minimize. 
     *
     * @return \EA\Engine\Api\V1\Response
     */
    public function minimize() {
        Processors\Minimize::process($this->response);

        return $this;
    }

    /**
     * Return a single entry instead of an array of entries. 
     *
     * This is useful whenever the client requests only a single entry. Make sure that you call this method
     * right before the output() in order to avoid side-effects with the other processor methods of this class. 
     *
     * @param int $id Provide the ID value of the request UI and if is not null the response will be 
     * converted into an associative array.
     * 
     * @return \EA\Engine\Api\V1\Response
     */
    public function singleEntry($id) {
        if ($id !== null) {
            $this->response = array_shift($this->response);
        }
        
        return $this;
    }

    /**
     * Output the response as a JSON with the provided status header. 
     *
     * @param \EA\Engine\Types\NonEmptyText $status Optional (null), if provided it must contain the status
     * header value. Default string: '200 OK'.
     *
     * @return \EA\Engine\Api\V1\Response
     */
    public function output(NonEmptyText $status = null) {
        $header = $status ? $status->get() : '200 OK'; 

        header('HTTP/1.0 ' . $header); 

        echo json_encode($this->response, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        return $this;
    }
}
