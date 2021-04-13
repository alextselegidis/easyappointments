<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @author	CodeIgniter community
 * @author	
 * @copyright	Copyright (c) 2014-2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['migration_none_found']		= 'No s\'ha trobat cap migració.';
$lang['migration_not_found']		= 'No s\'ha trobat cap migració amb el númeo de versió: %s.';
$lang['migration_sequence_gap']		= 'Hi ha un buit a la migració, prop del número de versió: %s.';
$lang['migration_multiple_version']	= 'Hi ha múltiples migracions amb el mateix número de versió: %s.';
$lang['migration_class_doesnt_exist']	= 'La classe de migració "%s" no s\'ha trobat.';
$lang['migration_missing_up_method']	= 'A la classe de migració "%s", li falta el mètode "up".';
$lang['migration_missing_down_method']	= 'A la classe de migració "%s" li falta el mètode "down".';
$lang['migration_invalid_filename']	= 'La migració "%s" té un nom de fitxer no vàlid.';
