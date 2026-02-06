<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
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
 * @copyright    Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('Direktaccess till skriptet är inte tillåtet');

$lang['imglib_source_image_required'] = 'Du måste ange en källbild i dina inställningar.';
$lang['imglib_gd_required'] = 'GD-bildbiblioteket krävs för den här funktionen.';
$lang['imglib_gd_required_for_props'] = 'Din server måste stödja GD-bildbiblioteket för att kunna bestämma bildegenskaperna.';
$lang['imglib_unsupported_imagecreate'] = 'Din server stöder inte den GD-funktion som krävs för att bearbeta denna typ av bild.';
$lang['imglib_gif_not_supported'] = 'GIF-bilder stöds ofta inte på grund av licensbegränsningar. Du kanske måste använda JPG- eller PNG-bilder istället.';
$lang['imglib_jpg_not_supported'] = 'JPG-bilder stöds inte.';
$lang['imglib_png_not_supported'] = 'PNG-bilder stöds inte.';
$lang['imglib_jpg_or_png_required'] = 'Protokollet för bildstorleksändring som anges i dina inställningar fungerar bara med JPEG- eller PNG-bildtyper.';
$lang['imglib_copy_error'] = 'Ett fel påträffades vid försök att ersätta filen. Se till att din filkatalog är skrivbar.';
$lang['imglib_rotate_unsupported'] = 'Rotation av bilder verkar inte stödjas av din server.';
$lang['imglib_libpath_invalid'] = 'Sökvägen till ditt bildbibliotek är inte korrekt. Ange rätt sökväg i dina bildinställningars.';
$lang['imglib_image_process_failed'] = 'Bildbehandlingen misslyckades. Kontrollera att din server stöder det valda protokollet och att sökvägen till ditt bildbibliotek är korrekt.';
$lang['imglib_rotation_angle_required'] = 'En rotationsvinkel krävs för att rotera bilden.';
$lang['imglib_invalid_path'] = 'Sökvägen till bilden är inte korrekt.';
$lang['imglib_invalid_image'] = 'Den angivna bilden är inte giltig.';
$lang['imglib_copy_failed'] = 'Bildkopieringsrutinen misslyckades.';
$lang['imglib_missing_font'] = 'Det gick inte att hitta ett teckensnitt att använda.';
$lang['imglib_save_failed'] = 'Det gick inte att spara bilden. Se till att bilden och filkatalogen är skrivbara.';
