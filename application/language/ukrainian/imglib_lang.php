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
defined('BASEPATH') or exit('No direct script access allowed');

$lang['imglib_source_image_required'] = 'У налаштуваннях потрібно вказати вихідне зображення.';
$lang['imglib_gd_required'] = 'Для цієї функції потрібна бібліотека зображень GD.';
$lang['imglib_gd_required_for_props'] = 'Ваш сервер повинен підтримувати бібліотеку GD для визначення властивостей зображення.';
$lang['imglib_unsupported_imagecreate'] = 'Ваш сервер не підтримує функцію GD, необхідну для обробки цього типу зображень.';
$lang['imglib_gif_not_supported'] = 'Зображення GIF часто не підтримуються через обмеження ліцензії. Спробуйте використати JPG або PNG.';
$lang['imglib_jpg_not_supported'] = 'Зображення JPG не підтримуються.';
$lang['imglib_png_not_supported'] = 'Зображення PNG не підтримуються.';
$lang['imglib_jpg_or_png_required'] = 'Протокол зміни розміру зображень, вказаний у налаштуваннях, працює лише з типами зображень JPEG або PNG.';
$lang['imglib_copy_error'] = 'Виникла помилка під час спроби замінити файл. Переконайтеся, що директорія доступна для запису.';
$lang['imglib_rotate_unsupported'] = 'Обертання зображень, ймовірно, не підтримується вашим сервером.';
$lang['imglib_libpath_invalid'] = 'Шлях до вашої бібліотеки зображень неправильний. Укажіть правильний шлях у налаштуваннях.';
$lang['imglib_image_process_failed'] = 'Не вдалося обробити зображення. Перевірте, чи підтримує сервер обраний протокол і чи правильно вказано шлях до бібліотеки зображень.';
$lang['imglib_rotation_angle_required'] = 'Для обертання зображення потрібно вказати кут.';
$lang['imglib_invalid_path'] = 'Шлях до зображення вказано неправильно.';
$lang['imglib_invalid_image'] = 'Надане зображення є недійсним.';
$lang['imglib_copy_failed'] = 'Не вдалося скопіювати зображення.';
$lang['imglib_missing_font'] = 'Не вдалося знайти шрифт для використання.';
$lang['imglib_save_failed'] = 'Не вдалося зберегти зображення. Переконайтеся, що зображення та директорія доступні для запису.';
