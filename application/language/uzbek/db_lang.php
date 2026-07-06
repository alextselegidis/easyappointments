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

$lang['db_invalid_connection_str'] = 'Kiritilgan ulanish satri asosida ma\'lumotlar bazasi sozlamalarini aniqlab bo\'lmadi.';
$lang['db_unable_to_connect'] = 'Berilgan sozlamalar bilan ma\'lumotlar bazasi serveriga ulanib bo\'lmadi.';
$lang['db_unable_to_select'] = 'Ko\'rsatilgan ma\'lumotlar bazasini tanlab bo\'lmadi: %s';
$lang['db_unable_to_create'] = 'Ko\'rsatilgan ma\'lumotlar bazasini yaratib bo\'lmadi: %s';
$lang['db_invalid_query'] = 'Yuborilgan so\'rov yaroqsiz.';
$lang['db_must_set_table'] = 'So\'rov uchun ishlatiladigan jadvalni belgilash kerak.';
$lang['db_must_use_set'] = 'Yozuvni yangilash uchun "set" metodidan foydalanish kerak.';
$lang['db_must_use_index'] = 'Ommaviy yangilash uchun moslashtiriladigan indeks ko\'rsatilishi kerak.';
$lang['db_batch_missing_index'] = 'Ommaviy yangilash uchun yuborilgan qatorlardan bir yoki bir nechtasida ko\'rsatilgan indeks yo\'q.';
$lang['db_must_use_where'] = 'Tarkibida "where" sharti bo\'lmasa, yangilashga ruxsat yo\'q.';
$lang['db_del_must_use_where'] = 'Tarkibida "where" yoki "like" sharti bo\'lmasa, o\'chirishga ruxsat yo\'q.';
$lang['db_field_param_missing'] = 'Maydonlarni olish uchun parametr sifatida jadval nomi kerak.';
$lang['db_unsupported_function'] = 'Bu funksiya siz ishlatayotgan ma\'lumotlar bazasida mavjud emas.';
$lang['db_transaction_failure'] = 'Tranzaksiya xatosi: rollback bajarildi.';
$lang['db_unable_to_drop'] = 'Ko\'rsatilgan ma\'lumotlar bazasini o\'chirib bo\'lmadi.';
$lang['db_unsupported_feature'] = 'Siz ishlatayotgan ma\'lumotlar bazasi platformasida bu funksiya qo\'llab-quvvatlanmaydi.';
$lang['db_unsupported_compression'] = 'Tanlangan fayl siqish formati serveringizda qo\'llab-quvvatlanmaydi.';
$lang['db_filepath_error'] = 'Ko\'rsatilgan fayl yo\'liga ma\'lumot yozib bo\'lmadi.';
$lang['db_invalid_cache_path'] = 'Yuborilgan kesh yo\'li yaroqsiz yoki yozish uchun mavjud emas.';
$lang['db_table_name_required'] = 'Bu amal uchun jadval nomi kerak.';
$lang['db_column_name_required'] = 'Bu amal uchun ustun nomi kerak.';
$lang['db_column_definition_required'] = 'Bu amal uchun ustun ta\'rifi kerak.';
$lang['db_unable_to_set_charset'] = 'Klient ulanishi uchun belgilar kodlashni o\'rnatib bo\'lmadi: %s';
$lang['db_error_heading'] = 'Ma\'lumotlar bazasi xatosi yuz berdi';
