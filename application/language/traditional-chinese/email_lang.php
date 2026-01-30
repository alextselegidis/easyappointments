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

$lang['email_must_be_array'] = '電子郵件驗證方法必須傳入一個陣列。';
$lang['email_invalid_address'] = '無效的電子郵件地址：%s';
$lang['email_attachment_missing'] = '無法找到以下電子郵件附件：%s';
$lang['email_attachment_unreadable'] = '無法開啟此附件：%s';
$lang['email_no_from'] = '無法傳送沒有「寄件人」標頭的郵件。';
$lang['email_no_recipients'] = '您必須包含收件人：收件人、副本或密件副本';
$lang['email_send_failure_phpmail'] = '無法使用 PHP mail() 傳送電子郵件。您的伺服器可能未設定為使用此方法傳送電子郵件。';
$lang['email_send_failure_sendmail'] = '無法使用 PHP Sendmail 傳送電子郵件。您的伺服器可能未設定為使用此方法傳送電子郵件。';
$lang['email_send_failure_smtp'] = '無法使用 PHP SMTP 傳送電子郵件。您的伺服器可能未設定為使用此方法傳送電子郵件。';
$lang['email_sent'] = '您的訊息已成功使用以下協定傳送：%s';
$lang['email_no_socket'] = '無法開啟 Sendmail 的通訊端。請檢查設定。';
$lang['email_no_hostname'] = '您未指定 SMTP 主機名稱。';
$lang['email_smtp_error'] = '遇到以下 SMTP 錯誤：%s';
$lang['email_no_smtp_unpw'] = '錯誤：您必須指定 SMTP 使用者名稱和密碼。';
$lang['email_failed_smtp_login'] = '無法傳送 AUTH LOGIN 指令。錯誤：%s';
$lang['email_smtp_auth_un'] = '無法驗證使用者名稱。錯誤：%s';
$lang['email_smtp_auth_pw'] = '無法驗證密碼。錯誤：%s';
$lang['email_smtp_data_failure'] = '無法傳送資料：%s';
$lang['email_exit_status'] = '結束狀態碼：%s';
