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

$lang['email_must_be_array'] = 'Методу валідації email необхідно передати масив.';
$lang['email_invalid_address'] = 'Невірна email-адреса: %s';
$lang['email_attachment_missing'] = 'Не вдалося знайти вкладення до листа: %s';
$lang['email_attachment_unreadable'] = 'Не вдалося відкрити вкладення: %s';
$lang['email_no_from'] = 'Неможливо надіслати лист без заголовка "From".';
$lang['email_no_recipients'] = 'Необхідно вказати одержувачів: To, Cc або Bcc';
$lang['email_send_failure_phpmail'] = 'Не вдалося надіслати лист за допомогою PHP mail(). Можливо, сервер не налаштований для використання цього методу.';
$lang['email_send_failure_sendmail'] = 'Не вдалося надіслати лист за допомогою PHP Sendmail. Можливо, сервер не налаштований для використання цього методу.';
$lang['email_send_failure_smtp'] = 'Не вдалося надіслати лист за допомогою PHP SMTP. Можливо, сервер не налаштований для використання цього методу.';
$lang['email_sent'] = 'Ваше повідомлення було успішно надіслано з використанням наступного протоколу: %s';
$lang['email_no_socket'] = 'Не вдалося відкрити сокет до Sendmail. Перевірте налаштування.';
$lang['email_no_hostname'] = 'Не вказано ім’я хоста SMTP.';
$lang['email_smtp_error'] = 'Виникла наступна помилка SMTP: %s';
$lang['email_no_smtp_unpw'] = 'Помилка: необхідно вказати ім’я користувача та пароль SMTP.';
$lang['email_failed_smtp_login'] = 'Не вдалося надіслати команду AUTH LOGIN. Помилка: %s';
$lang['email_smtp_auth_un'] = 'Не вдалося авторизувати ім’я користувача. Помилка: %s';
$lang['email_smtp_auth_pw'] = 'Не вдалося авторизувати пароль. Помилка: %s';
$lang['email_smtp_data_failure'] = 'Не вдалося надіслати дані: %s';
$lang['email_exit_status'] = 'Код завершення: %s';
