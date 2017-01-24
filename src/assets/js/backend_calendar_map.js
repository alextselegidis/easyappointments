/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      M.Venturi <matteo.venturi7@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

window.BackendCalendarMap = window.BackendCalendarMap || {};

/**
 * Backend Calendar
 *
 * This module implements the table calendar view of backend.
 *
 * @module BackendCalendarTableView
 */
(function(exports) {

    'use strict';

    var mapLinkTemplate = '<a target="_blank" href="https://www.google.com/maps/place/{$full_address}">'+EALang['open_map']+'</a>';

    exports.getMapLink = function(customer){
        var map_link = null;

        if(!!customer){            
            if(!!customer.address){
                var full_address = customer.address;

                if(!!customer.city)
                    full_address +=','+customer.city;

                if(!!customer.zip_code)
                    full_address +=','+customer.zip_code;

                map_link = mapLinkTemplate.replace('{$full_address}', full_address.replace(/ /g,'+'));
            }
        }

        return map_link;
    }

})(window.BackendCalendarMap);