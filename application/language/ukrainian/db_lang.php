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

$lang['db_invalid_connection_str'] = 'Неможливо визначити налаштування бази даних на основі наданого рядка підключення.';
$lang['db_unable_to_connect'] = 'Не вдалося підключитися до сервера бази даних за наданими налаштуваннями.';
$lang['db_unable_to_select'] = 'Не вдалося вибрати вказану базу даних: %s';
$lang['db_unable_to_create'] = 'Не вдалося створити вказану базу даних: %s';
$lang['db_invalid_query'] = 'Запит, який ви надіслали, є недійсним.';
$lang['db_must_set_table'] = 'Ви повинні вказати таблицю бази даних для використання в запиті.';
$lang['db_must_use_set'] = 'Ви повинні використовувати метод "set" для оновлення запису.';
$lang['db_must_use_index'] = 'Потрібно вказати індекс для порівняння під час пакетного оновлення.';
$lang['db_batch_missing_index'] = 'Один або кілька рядків для пакетного оновлення не містять вказаного індексу.';
$lang['db_must_use_where'] = 'Оновлення дозволені лише за наявності умови "where".';
$lang['db_del_must_use_where'] = 'Видалення дозволене лише за наявності умови "where" або "like".';
$lang['db_field_param_missing'] = 'Для отримання полів необхідно вказати назву таблиці як параметр.';
$lang['db_unsupported_function'] = 'Ця функція недоступна для обраної бази даних.';
$lang['db_transaction_failure'] = 'Помилка транзакції: виконано відкат.';
$lang['db_unable_to_drop'] = 'Не вдалося видалити вказану базу даних.';
$lang['db_unsupported_feature'] = 'Непідтримувана функція платформи бази даних.';
$lang['db_unsupported_compression'] = 'Формат стиснення файлів, який ви обрали, не підтримується сервером.';
$lang['db_filepath_error'] = 'Неможливо записати дані за вказаним шляхом до файлу.';
$lang['db_invalid_cache_path'] = 'Надано недійсний або недоступний шлях до кешу.';
$lang['db_table_name_required'] = 'Потрібна назва таблиці для цієї операції.';
$lang['db_column_name_required'] = 'Потрібна назва стовпця для цієї операції.';
$lang['db_column_definition_required'] = 'Потрібне визначення стовпця для цієї операції.';
$lang['db_unable_to_set_charset'] = 'Не вдалося встановити кодування клієнтського з’єднання: %s';
$lang['db_error_heading'] = 'Сталася помилка бази даних';
