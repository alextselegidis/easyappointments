<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Print Google Analytics script.
 *
 * This helper function should be used in view files in order to output the Google Analytics script. It will check
 * whether the code is set in the database and print it, otherwise nothing will be outputted. This eliminates the need
 * for extra checking before outputting.
 */
function google_analytics_script()
{
    $CI =& get_instance();

    $CI->load->model('settings_model');

    $google_analytics_code = $CI->settings_model->get_setting('google_analytics_code');

    if ($google_analytics_code !== '') {

        // If the google analytics code starts with UA then it is a Universal Analytics Property and the script stays
        // the legacy one
        if (substr($google_analytics_code, 0, 2) === "UA") {
            echo '
            <script>
                (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,"script","//www.google-analytics.com/analytics.js","ga");
                ga("create", "' . $google_analytics_code . '", "auto");
                ga("send", "pageview");
            </script>
        ';
        }

        // If the google analytics code starts with a G then it is a Google Analytics 4-Property and the script 
        // to inject it looks different.
        if (substr($google_analytics_code, 0, 2) === "G-") {
            echo '
                    <script async src="https://www.googletagmanager.com/gtag/js?id=' . $google_analytics_code . '"></script>
                    <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag("js", new Date());
                    gtag("config", "' . $google_analytics_code . '");
                    </script>
                ';
        }
    }
}
