<?php
/**
 * Systemmeldinger for CodeIgniter(tm)
 *
 * @forfatter    CodeIgniter-samfunnet
 * @opphavsrett    Copyright (c) 2014-2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @lisens    http://opensource.org/licenses/MIT	MIT-lisens
 * @link    https://codeigniter.com
 */
defined('BASEPATH') or exit('Direkte tilgang til skriptet er ikke tillatt');

$lang['migration_none_found'] = 'Ingen migrasjoner funnet.';
$lang['migration_not_found'] = 'Ingen migrasjoner funnet med versjonsnummer: %s.';
$lang['migration_sequence_gap'] = 'Det mangler deler i migrasjonssekvensen for versjonsnummer: %s.';
$lang['migration_multiple_version'] = 'Det finnes flere migrasjoner med samme versjonsnummer: %s.';
$lang['migration_class_doesnt_exist'] = 'Migrasjonsklassen "%s" ble ikke funnet.';
$lang['migration_missing_up_method'] = 'Migrasjonsklassen "%s" mangler "up"-metoden.';
$lang['migration_missing_down_method'] = 'Migrasjonsklassen "%s" mangler "down"-metoden.';
$lang['migration_invalid_filename'] = 'Migrasjonen "%s" har et ugyldig filnavn.';
