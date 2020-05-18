<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @author	CodeIgniter community
 * @copyright	Copyright (c) 2014-2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['db_invalid_connection_str'] = 'Det går inte att avgöra databasinställningarna utifrån den angivna anslutningssträngen.';
$lang['db_unable_to_connect'] = 'Kan inte ansluta till databasen med de angivna inställningarna.';
$lang['db_unable_to_select'] = 'Kan inte välja den angivna databasen: %s';
$lang['db_unable_to_create'] = 'Kan inte skapa den angivna databasen: %s';
$lang['db_invalid_query'] = 'Frågan du skickade är inte giltig.';
$lang['db_must_set_table'] = 'Du måste ange vilken tabell som ska användas i databasfrågan.';
$lang['db_must_use_set'] = 'Du måste använda "set"-metoden för att uppdatera en post.';
$lang['db_must_use_index'] = 'Du måste ange ett index att matcha mot vid batchuppdateringar.';
$lang['db_batch_missing_index'] = 'En eller flera rader skickade för batchuppdatering saknar det angivna indexet.';
$lang['db_must_use_where'] = 'Uppdateringar utan "where" är inte tillåtna.';
$lang['db_del_must_use_where'] = 'Raderingar utan "where" eller "like" är inte tillåtna.';
$lang['db_field_param_missing'] = 'För att hämta fält krävs namnet på tabellen som parameter.';
$lang['db_unsupported_function'] = 'Den här funktionen finns inte i den databas du använder.';
$lang['db_transaction_failure'] = 'Transaktionen gick inte igenom och rullas tillbaka.';
$lang['db_unable_to_drop'] = 'Det går inte att ta bort den angivna databasen.';
$lang['db_unsupported_feature'] = 'Den här funktionen finns inte i den databashanterare du använder.';
$lang['db_unsupported_compression'] = 'Formatet för filkompression stöds inte av din server.';
$lang['db_filepath_error'] = 'Det går inte att skriva till den filsökväg du angivit.';
$lang['db_invalid_cache_path'] = 'Cache-sökvägen du angivit är ogiltig eller inte skrivbar.';
$lang['db_table_name_required'] = 'Ett tabellnamn behövs för denna operation.';
$lang['db_column_name_required'] = 'Ett kolumnnamn behövs för denna operation.';
$lang['db_column_definition_required'] = 'En kolumndefinition behövs för denna operation.';
$lang['db_unable_to_set_charset'] = 'Kan inte använda klientens teckenuppsättning: %s';
$lang['db_error_heading'] = 'Det uppstod ett databasfel';
