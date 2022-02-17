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

$lang['email_must_be_array'] = 'E-mail 效驗方法必須傳入一個 Array';
$lang['email_invalid_address'] = '無效的 E-mail 地址： %s';
$lang['email_attachment_missing'] = '無法找到以下的 E-mail 附件： %s';
$lang['email_attachment_unreadable'] = '無法讀取以下的 E-mail 附件： %s';
$lang['email_no_from'] = '無法發送沒有 "From" 頭的 E-mail';
$lang['email_no_recipients'] = 'E-mail 必須包含收件人（To, Cc, or Bcc）';
$lang['email_send_failure_phpmail'] = '無法使用 PHP 的 mail() 函數。您的服務器設置禁止使用此函數發送 E-mail。 ';
$lang['email_send_failure_sendmail'] = '無法使用 PHP sendmail。您的服務器設置禁止使用此方法發送 E-mail。 ';
$lang['email_send_failure_smtp'] = '無法使用 PHP SMTP。您的服務器設置禁止使用此方法發送 E-mail。 ';
$lang['email_sent'] = 'E-mail 成功發送： %s';
$lang['email_no_socket'] = '無法打開 Socket 發送 E-mail，檢查設置。 ';
$lang['email_no_hostname'] = '沒有指定 SMTP 服務器的主機名';
$lang['email_smtp_error'] = '發生錯誤，SMTP 錯誤信息為： %s';
$lang['email_no_smtp_unpw'] = '錯誤：必須指定 SMTP 的用戶名及密碼。 ';
$lang['email_failed_smtp_login'] = '發送時 AUTH 命令失敗，錯誤：%s';
$lang['email_smtp_auth_un'] = '用戶名認證失敗，錯誤：%s';
$lang['email_smtp_auth_pw'] = '密碼認證失敗，錯誤：%s';
$lang['email_smtp_data_failure'] = '無法發送數據：%s';
$lang['email_exit_status'] = '退出狀態碼：%s';
