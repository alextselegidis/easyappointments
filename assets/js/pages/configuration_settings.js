/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.0
 * ---------------------------------------------------------------------------- */

/**
 * General settings page.
 *
 * This module implements the functionality of the configuration settings page.
 */
App.Pages.ConfigurationSettings = (function () {
    const $configSettingsTbl = $('.configs-table tbody');
    const $fieldBase = $('<input/>', {
        'type': 'text',
        'class': 'form-control mb2',
        'data-field': '',
        'data-idx': '',
        'value': '',
    });

    const $btnTmpl = $('<button/>', {
        'class': 'btn',
        'type': 'button',
        'data-row': '',
        'html': [
            $('<i/>', {
                'class': 'fas fa-trash',
            }),
        ]
    });

    let configSettings = [];
    let deletedSettings = [];

    function deleteCfgItm(event) {
        const $btn = $(event.currentTarget);
        const idx = parseInt($btn.attr('data-row'));
        
        deletedSettings.push(configSettings[idx]);
        configSettings.splice(idx,1);

        refreshTable();
    }

    function field(itm, name, editable = true, required = false) {
        const $cell = $fieldBase.clone();
        $cell.attr('data-field', name);
        $cell.attr('data-idx', itm['index']);
        $cell.val(itm[name]);
        $cell.attr('readonly', !editable);
        if (required) {$cell.addClass('required');}

        return $('<td/>').append($cell);
    }

    function row(cfgItem) {
        const $btn = $btnTmpl.clone();
        const $field = $fieldBase.clone();

        $btn.attr('data-row', cfgItem['index']);
        $btn.on('click', (event) => deleteCfgItm(event));;

        const $row = $('<tr/>');
        $field.attr('data-field', 'name');
        $field.attr('data-idx', cfgItem['index']);
        $field.val(cfgItem['name']);
        $row.append($('<td/>').append($field.clone().attr('readonly', !(cfgItem['id'] == null)).addClass('required')));

        $field.attr('data-field', 'value');
        $field.attr('data-idx', cfgItem['index']);
        $field.val(cfgItem['value']);
        $row.append($('<td/>').append($field.clone()));

        $field.attr('data-field', 'description');
        $field.attr('data-idx', cfgItem['index']);
        $field.val(cfgItem['description']);
        $row.append($('<td/>').append($field.clone()));

        // $row.append(field(cfgItem,'name', (cfgItem['id'] == null), true));
        // $row.append(field(cfgItem,'value'));
        // $row.append(field(cfgItem,'description'));
        $row.append($('<td/>').append($btn));
        

        return $row;
    }

    function refreshTable() {
        $configSettingsTbl.empty();
        let idx = 0;
        configSettings.forEach((cfg) => {
            cfg['index'] = idx;
            row(cfg).appendTo($configSettingsTbl);
            idx ++;
        });
    }

    function addRow() {
        const cfg = {
            'id': null,
        };
        configSettings.push(cfg);
        serialize();
        refreshTable();
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        // Add eventlisteneers
        $('#add-row').on('click', addRow);

        $('#save-config').on('click', save);

        $('#reset-config').on('click', reset);

        reset();
    }

    function reset() {
        // Get current config values
        // Use $.extend to get a real deep copy of the array
        configSettings = $.extend(true, [], vars('config_settings'));
        deletedSettings = [];

        // Draw table
        refreshTable();
    }

    /**
     * Serialize the content of the table into the configSettings array
     */
    function serialize() {
        const $fields = $configSettingsTbl.find('input');

        $.each($fields, (i) => {
            const $field = $($fields[i]);
            const idx = $field.attr('data-idx');
            const name = $field.attr('data-field');
            configSettings[idx][name] = $field.val();
        });
        
    }

    /**
     * Save the configSettings array to the database
     */
    function save() {
        serialize();
        App.Http.ConfigurationSettings.save(configSettings);
        App.Http.ConfigurationSettings.remove(deletedSettings);

        // Reload the page so that vars('config_settings') is reloaded
        window.location.reload();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();