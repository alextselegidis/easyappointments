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

$lang['email_must_be_array'] = 'Email validatsiya metodiga massiv uzatilishi kerak.';
$lang['email_invalid_address'] = 'Noto\'g\'ri email manzil: %s';
$lang['email_attachment_missing'] = 'Quyidagi email ilova topilmadi: %s';
$lang['email_attachment_unreadable'] = 'Bu ilovani ochib bo\'lmadi: %s';
$lang['email_no_from'] = '"From" sarlavhasisiz xat yuborib bo\'lmaydi.';
$lang['email_no_recipients'] = 'Qabul qiluvchilarni ko\'rsatish kerak: To, Cc yoki Bcc';
$lang['email_send_failure_phpmail'] = 'PHP mail() orqali xat yuborib bo\'lmadi. Serveringiz bu usulda xat yuborishga sozlanmagan bo\'lishi mumkin.';
$lang['email_send_failure_sendmail'] = 'PHP Sendmail orqali xat yuborib bo\'lmadi. Serveringiz bu usulda xat yuborishga sozlanmagan bo\'lishi mumkin.';
$lang['email_send_failure_smtp'] = 'PHP SMTP orqali xat yuborib bo\'lmadi. Serveringiz bu usulda xat yuborishga sozlanmagan bo\'lishi mumkin.';
$lang['email_sent'] = 'Xabaringiz quyidagi protokol orqali muvaffaqiyatli yuborildi: %s';
$lang['email_no_socket'] = 'Sendmail uchun socket ochib bo\'lmadi. Sozlamalarni tekshiring.';
$lang['email_no_hostname'] = 'SMTP hostname ko\'rsatilmagan.';
$lang['email_smtp_error'] = 'Quyidagi SMTP xatosi yuz berdi: %s';
$lang['email_no_smtp_unpw'] = 'Xato: SMTP uchun username va parol ko\'rsatilishi kerak.';
$lang['email_failed_smtp_login'] = 'AUTH LOGIN buyrug\'ini yuborib bo\'lmadi. Xato: %s';
$lang['email_smtp_auth_un'] = 'Username autentifikatsiyasi muvaffaqiyatsiz. Xato: %s';
$lang['email_smtp_auth_pw'] = 'Parol autentifikatsiyasi muvaffaqiyatsiz. Xato: %s';
$lang['email_smtp_data_failure'] = 'Ma\'lumot yuborib bo\'lmadi: %s';
$lang['email_exit_status'] = 'Chiqish holati kodi: %s';
