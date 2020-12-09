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

$lang['db_invalid_connection_str'] = 'Z řetězce spojení které jste zadali nelze určit nastavení databáze.';
$lang['db_unable_to_connect'] = 'Se zadanými nastaveními se není možné připojit k databázovému serveru.';
$lang['db_unable_to_select'] = 'Není možné zvolit zadanou databázi: %s';
$lang['db_unable_to_create'] = 'Není možné vytvřit zadanou databázi: %s';
$lang['db_invalid_query'] = 'Dotaz který jste zadali není platný.';
$lang['db_must_set_table'] = 'Musíte zadat databázovou tabulku na kterou bude dotaz aplikován.';
$lang['db_must_use_set'] = 'Pro aktualizaci záznamu musíte použít metodu "set".';
$lang['db_must_use_index'] = 'Pro dávkovou aktualizaci musíte zadat index, u kterého budou změny aplikovány.';
$lang['db_batch_missing_index'] = 'Jeden nebo více záznamů zadaných pro dávkovou aktualizaci postrádá zadaný index.';
$lang['db_must_use_where'] = 'Aktualizace nejsou povoleny pokud neobsahují "where" klauzuli.';
$lang['db_del_must_use_where'] = 'Odstranění nejsou povolena pokud neobsahují "where" nebo "like" klauzuli.';
$lang['db_field_param_missing'] = 'Pro získání polí je vyžadováno zadání názvu tabulky jako parametru.';
$lang['db_unsupported_function'] = 'Tato funkce není v databázi kterou používáte dostupná.';
$lang['db_transaction_failure'] = 'Selhání transakce: proveden rollback.';
$lang['db_unable_to_drop'] = 'Není možné odstranit zadanou databázi.';
$lang['db_unsupported_feature'] = 'Funkce není na databázové platformě kterou používáte podporována.';
$lang['db_unsupported_compression'] = 'Formát komprese souborů který jste zvolili není vaším serverem podporován.';
$lang['db_filepath_error'] = 'Do umístění které jste zadali není možné data zapsat.';
$lang['db_invalid_cache_path'] = 'Umístění mezipaměti které jste zadali není platné nebo zapisovatelné.';
$lang['db_table_name_required'] = 'Pro tuto operaci je vyžadován název tabulky.';
$lang['db_column_name_required'] = 'Pro tuto operaci je vyžadován název sloupce.';
$lang['db_column_definition_required'] = 'Pro tuto operaci je vyžadována definice sloupce.';
$lang['db_unable_to_set_charset'] = 'Není možné nastavit znakovou sadu spojení klienta: %s';
$lang['db_error_heading'] = 'Vyskytla se chyba databáze';
