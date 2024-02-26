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

$lang['db_invalid_connection_str'] = 'Tietokannan asetuksia ei voitu päätellä antamastasi yhteystekstistä.';
$lang['db_unable_to_connect'] = 'Annetuilla määrityksilä ei voitu yhdistää tietokantaan.';
$lang['db_unable_to_select'] = 'Ei voitu yhdistää tietokantaan %s';
$lang['db_unable_to_create'] = 'Ei voitu luoda tietokantaa %s';
$lang['db_invalid_query'] = 'Antamasi kysely ei ole kelvollinen.';
$lang['db_must_set_table'] = 'Kyselylle on määritettävä taulu.';
$lang['db_must_use_set'] = 'Sinun tulee käyttää "set" metodia tiedon päivittämiseen.';
$lang['db_must_use_index'] = 'Sinun tulee määrittää indeksi mikä täsmää eräpäivityksille.';
$lang['db_batch_missing_index'] = 'Yhdeltä tai useammalta riviltä eräpäivityksessä puuttuu määritetty indeksi.';
$lang['db_must_use_where'] = 'Päivitykset ilman "where" lauseketta eivät ole sallittuja.';
$lang['db_del_must_use_where'] = 'Poistot ilman "where" tai "like" lauseketta eivät ole sallittuja.';
$lang['db_field_param_missing'] = 'Kenttien haku edellyttää taulun nimeä parametrina.';
$lang['db_unsupported_function'] = 'Tämä toiminto ei ole saatavilla käyttämässäsi tietokannassa.';
$lang['db_transaction_failure'] = 'Transaktiovirhe: Rollback suoritettu.';
$lang['db_unable_to_drop'] = 'Määritettyä tietokantaa ei voitu poistaa.';
$lang['db_unsupported_feature'] = 'Tämä ominaisuus ei ole saatavilla käyttämässäsi tietokannassa.';
$lang['db_unsupported_compression'] = 'Valitsemasi tiedoston pakkausmuoto ei ole käytettävissä palvelimella.';
$lang['db_filepath_error'] = 'Määrittelemääsi tiedostopolkuun ei voida kirjoittaa.';
$lang['db_invalid_cache_path'] = 'Määrittämäsi välimuistin tiedostopolku ei ole kelvollinen tai sinne ei voi kirjoittaa.';
$lang['db_table_name_required'] = 'Operaatiota varten tarvitaan taulun nimi.';
$lang['db_column_name_required'] = 'Operaatiota varten tarvitaan sarakkeen nimi.';
$lang['db_column_definition_required'] = 'Operaatiota varten tarvitaan sarakemääritys.';
$lang['db_unable_to_set_charset'] = 'Yhteydelle ei voitu määrittää merkistöä: %s';
$lang['db_error_heading'] = 'Tietokantavirhe';
