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

$lang['db_invalid_connection_str'] = '無法根據您提交的連線字串判斷資料庫設定。';
$lang['db_unable_to_connect'] = '無法使用提供的設定連線至您的資料庫伺服器。';
$lang['db_unable_to_select'] = '無法選擇指定的資料庫：%s';
$lang['db_unable_to_create'] = '無法建立指定的資料庫：%s';
$lang['db_invalid_query'] = '您提交的查詢無效。';
$lang['db_must_set_table'] = '您必須設定要在查詢中使用的資料表。';
$lang['db_must_use_set'] = '您必須使用 "set" 方法來更新項目。';
$lang['db_must_use_index'] = '您必須指定一個索引來進行批次更新。';
$lang['db_batch_missing_index'] = '批次更新中的一個或多個列缺少指定的索引。';
$lang['db_must_use_where'] = '除非包含 "where" 子句，否則不允許進行更新。';
$lang['db_del_must_use_where'] = '除非包含 "where" 或 "like" 子句，否則不允許進行刪除。';
$lang['db_field_param_missing'] = '取得欄位需要表格名稱作為參數。';
$lang['db_unsupported_function'] = '您使用的資料庫不支援此功能。';
$lang['db_transaction_failure'] = '交易失敗：已執行回復。';
$lang['db_unable_to_drop'] = '無法刪除指定的資料庫。';
$lang['db_unsupported_feature'] = '您使用的資料庫平台不支援此功能。';
$lang['db_unsupported_compression'] = '您的伺服器不支援您選擇的檔案壓縮格式。';
$lang['db_filepath_error'] = '無法將資料寫入您提交的檔案路徑。';
$lang['db_invalid_cache_path'] = '您提交的快取路徑無效或無法寫入。';
$lang['db_table_name_required'] = '此操作需要表格名稱。';
$lang['db_column_name_required'] = '此操作需要欄位名稱。';
$lang['db_column_definition_required'] = '此操作需要欄位定義。';
$lang['db_unable_to_set_charset'] = '無法設定用戶端連線字元集：%s';
$lang['db_error_heading'] = '發生資料庫錯誤';
