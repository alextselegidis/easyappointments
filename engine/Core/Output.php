<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Core;

/**
 * Class Output
 *
 * @package EA\Engine\Core
 */
class Output {
    /**
     * @var \EA\Engine\Core\Framework
     */
    private $framework;

    /**
     * Output constructor.
     *
     * @param Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Output HTML markup content.
     *
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function html($content, $statusCode = 200, array $headers = [])
    {
        $headers[] = 'Content-Type: text/html';
        $this->write($content, $statusCode, $headers);
    }

    /**
     * Output JSON encoded content.
     *
     * @param mixed $content
     * @param int $statusCode
     * @param array $headers
     */
    public function json($content, $statusCode = 200, array $headers = [])
    {
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->write($content, $statusCode, $headers);
    }

    /**
     * Output arbitrary content with custom status and headers.
     *
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function write($content, $statusCode = 200, array $headers = [])
    {
        $this->framework->output->set_status_header($statusCode);

        foreach ($headers as $header)
        {
            $this->framework->output->set_header($header);
        }

        $this->framework->output->set_output($content);
    }
}
