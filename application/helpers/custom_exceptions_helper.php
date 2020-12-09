<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Print an exception to an HTML text.
 *
 * This method is used to display exceptions in a way that is useful and easy for the user to see. It uses the
 * Bootstrap CSS accordion markup to display the message and when the user clicks on it the exception trace will be
 * revealed. We display only one exceptions at a time because the user needs to be able to display the details of each
 * exception separately.
 *
 * @param Exception $exception The exception to be displayed.
 *
 * @return string Returns the html markup of the exception.
 */
function exceptionToHtml($exception)
{
    return
        '<div class="accordion" id="error-accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse"
                            data-parent="#error-accordion" href="#error-technical">' . $exception->getMessage() . '
                    </a>
                </div>
                <div id="error-technical" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <pre>' . $exception->getTraceAsString() . '</pre>
                    </div>
                </div>
            </div>
        </div>';
}
