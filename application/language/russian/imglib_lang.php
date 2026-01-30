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

$lang['imglib_source_image_required'] = 'Вы должны указать исходное изображение в настройках.';
$lang['imglib_gd_required'] = 'Для этой функции требуется библиотека изображений GD.';
$lang['imglib_gd_required_for_props'] = 'Ваш сервер должен поддерживать библиотеку изображений GD, чтобы определить свойства изображения.';
$lang['imglib_unsupported_imagecreate'] = 'Ваш сервер не поддерживает функцию GD, необходимую для обработки этого типа изображений.';
$lang['imglib_gif_not_supported'] = 'Изображения GIF часто не поддерживаются из-за лицензионных ограничений. Возможно, вместо них придется использовать изображения JPG или PNG.';
$lang['imglib_jpg_not_supported'] = 'Изображения JPG не поддерживаются.';
$lang['imglib_png_not_supported'] = 'Изображения PNG не поддерживаются.';
$lang['imglib_jpg_or_png_required'] = 'Протокол изменения размера изображения, указанный в настройках, работает только с изображениями типа JPEG или PNG.';
$lang['imglib_copy_error'] = 'При попытке заменить файл возникла ошибка. Пожалуйста, убедитесь, что ваш каталог файлов доступен для записи.';
$lang['imglib_rotate_unsupported'] = 'Похоже, что поворот изображения не поддерживается вашим сервером.';
$lang['imglib_libpath_invalid'] = 'Неправильно указан путь к библиотеке изображений. Пожалуйста, установите правильный путь в настройках изображения.';
$lang['imglib_image_process_failed'] = 'Сбой обработки изображения. Убедитесь, что ваш сервер поддерживает выбранный протокол и что путь к библиотеке изображений указан правильно.';
$lang['imglib_rotation_angle_required'] = 'Для поворота изображения необходим угол поворота.';
$lang['imglib_invalid_path'] = 'Путь к изображению указан неверно.';
$lang['imglib_invalid_image'] = 'Предоставленное изображение недействительно.';
$lang['imglib_copy_failed'] = 'Не удалось выполнить копирование изображения.';
$lang['imglib_missing_font'] = 'Невозможно найти шрифт для использования.';
$lang['imglib_save_failed'] = 'Невозможно сохранить изображение. Убедитесь, что изображение и каталог файла доступны для записи.';
