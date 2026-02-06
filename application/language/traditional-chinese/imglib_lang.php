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

$lang['imglib_source_image_required'] = '您必須在偏好設定中指定來源圖片。';
$lang['imglib_gd_required'] = '此功能需要 GD 影像函式庫。';
$lang['imglib_gd_required_for_props'] = '您的伺服器必須支援 GD 影像函式庫才能確定圖片屬性。';
$lang['imglib_unsupported_imagecreate'] = '您的伺服器不支援處理此類圖片所需的 GD 函式。';
$lang['imglib_gif_not_supported'] = '由於授權限制，GIF 圖片通常不被支援。您可能需要改用 JPG 或 PNG 圖片。';
$lang['imglib_jpg_not_supported'] = 'JPG 圖片不被支援。';
$lang['imglib_png_not_supported'] = 'PNG 圖片不被支援。';
$lang['imglib_jpg_or_png_required'] = '您在偏好設定中指定的圖片調整協定˙僅適用於 JPEG 或 PNG 圖片類型。';
$lang['imglib_copy_error'] = '嘗試替換檔案時發生錯誤。請確保您的檔案目錄可寫入。';
$lang['imglib_rotate_unsupported'] = '您的伺服器似乎不支援圖片旋轉功能。';
$lang['imglib_libpath_invalid'] = '影像函式庫的路徑不正確。請在圖片偏好設定中設定正確的路徑。';
$lang['imglib_image_process_failed'] = '圖片處理失敗。請確認您的伺服器支援所選協定，並且影像函式庫的路徑正確。';
$lang['imglib_rotation_angle_required'] = '旋轉圖片需要指定旋轉角度。';
$lang['imglib_invalid_path'] = '圖片的路徑不正確。';
$lang['imglib_invalid_image'] = '提供的圖片無效。';
$lang['imglib_copy_failed'] = '圖片複製程序失敗。';
$lang['imglib_missing_font'] = '無法找到可用的字型。';
$lang['imglib_save_failed'] = '無法儲存圖片。請確保圖片和檔案目錄可寫入。';
