<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

$lang['db_invalid_connection_str'] = 'Kunne ikke bestemme databaseinnstillingene basert på tilkoblingsstrengen du sendte inn.';
$lang['db_unable_to_connect'] = 'Kunne ikke koble til databaseserveren din ved hjelp av de angitte innstillingene.';
$lang['db_unable_to_select'] = 'Kunne ikke velge den angitte databasen: %s';
$lang['db_unable_to_create'] = 'Kunne ikke opprette den angitte databasen: %s';
$lang['db_invalid_query'] = 'Spørringen du sendte inn, er ikke gyldig.';
$lang['db_must_set_table'] = 'Du må angi databasetabellen som skal brukes med spørringen din.';
$lang['db_must_use_set'] = 'Du må bruke ‘set’-metoden for å oppdatere en oppføring.';
$lang['db_must_use_index'] = 'Du må spesifisere en indeks å matche på for batchoppdateringer.';
$lang['db_batch_missing_index'] = 'En eller flere rader som er sendt inn for batchoppdatering, mangler den angitte indeksen.';
$lang['db_must_use_where'] = 'Oppdateringer er ikke tillatt med mindre de inneholder en \'where\'-klausul.';
$lang['db_del_must_use_where'] = 'Sletting er ikke tillatt med mindre de inneholder en ‘where’- eller ‘like’-klausul.';
$lang['db_field_param_missing'] = 'For å hente felt kreves navnet på tabellen som en parameter.';
$lang['db_unsupported_function'] = 'Denne funksjonen er ikke tilgjengelig for databasen du bruker.';
$lang['db_transaction_failure'] = 'Transaksjonsfeil: Rollback utført.';
$lang['db_unable_to_drop'] = 'Kunne ikke slette den angitte databasen.';
$lang['db_unsupported_feature'] = 'Funksjon som ikke støttes av databaseplattformen du bruker.';
$lang['db_unsupported_compression'] = 'Filkomprimeringsformatet du valgte, støttes ikke av serveren din.';
$lang['db_filepath_error'] = 'Kunne ikke skrive data til filbanen du har sendt inn.';
$lang['db_invalid_cache_path'] = 'Cache-banen du sendte inn, er ikke gyldig eller skrivbar.';
$lang['db_table_name_required'] = 'Et tabellnavn er påkrevd for denne operasjonen.';
$lang['db_column_name_required'] = 'Et kolonnenavn er påkrevd for denne operasjonen.';
$lang['db_column_definition_required'] = 'En kolonnedefinisjon er påkrevd for denne operasjonen.';
$lang['db_unable_to_set_charset'] = 'Kunne ikke angi tegnsett for klienttilkobling: %s';
$lang['db_error_heading'] = 'En databasefeil oppstod';
