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

$lang['db_invalid_connection_str'] = 'Nuk mund të përcaktohen cilësimet e bazës së të dhënave bazuar në vargun e lidhjes që keni dorëzuar.';
$lang['db_unable_to_connect'] = 'Nuk mund të lidhet me serverin e bazës së të dhënave duke përdorur cilësimet e dhëna.';
$lang['db_unable_to_select'] = 'Nuk mund të zgjidhet baza e të dhënave e specifikuar: %s';
$lang['db_unable_to_create'] = 'Nuk mund të krijohet baza e të dhënave e specifikuar: %s';
$lang['db_invalid_query'] = 'Query që keni dorëzuar nuk është e vlefshme.';
$lang['db_must_set_table'] = 'Duhet të caktoni tabelën e bazës së të dhënave që do të përdoret me pyetjen tuaj.';
$lang['db_must_use_set'] = 'Duhet të përdorni metodën "set" për të përditësuar një hyrje.';
$lang['db_must_use_index'] = 'Duhet të specifikoni një indeks për të përputhur për përditësime grupore.';
$lang['db_batch_missing_index'] = 'Një ose më shumë rreshta të dorëzuar për përditësime grupore mungojnë në indeksin e specifikuar.';
$lang['db_must_use_where'] = 'Përditësimet nuk lejohen nëse nuk përmbajnë një klauzolë "where".';
$lang['db_del_must_use_where'] = 'Fshirjet nuk lejohen nëse nuk përmbajnë një klauzolë "where" ose "like".';
$lang['db_field_param_missing'] = 'Për të marrë fushat kërkohet emri i tabelës si parametër.';
$lang['db_unsupported_function'] = 'Kjo veçori nuk është e disponueshme për bazën e të dhënave që po përdorni.';
$lang['db_transaction_failure'] = 'Dështim transaksioni: U krye rikthim.';
$lang['db_unable_to_drop'] = 'Nuk mund të fshihet baza e të dhënave e specifikuar.';
$lang['db_unsupported_feature'] = 'Veçori e papërkrahur e platformës së bazës së të dhënave që po përdorni.';
$lang['db_unsupported_compression'] = 'Formati i kompresimit të skedarëve që keni zgjedhur nuk mbështetet nga serveri juaj.';
$lang['db_filepath_error'] = 'Nuk mund të shkruhet e dhëna në shtegun e skedarit që keni dorëzuar.';
$lang['db_invalid_cache_path'] = 'Shtegu i cache që keni dorëzuar nuk është i vlefshëm ose i shkrueshëm.';
$lang['db_table_name_required'] = 'Është i nevojshëm emri i tabelës për atë operacion.';
$lang['db_column_name_required'] = 'Është i nevojshëm emri i kolonës për atë operacion.';
$lang['db_column_definition_required'] = 'Është i nevojshëm një përkufizim kolone për atë operacion.';
$lang['db_unable_to_set_charset'] = 'Nuk mund të caktohet grupi i karaktereve të lidhjes së klientit: %s';
$lang['db_error_heading'] = 'Ndodhi një Gabim në Bazën e të Dhënave';
