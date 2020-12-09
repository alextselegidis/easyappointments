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

$lang['email_must_be_array'] = 'שיטת אימות הדוא"ל חחיבת להיות במערך.';
$lang['email_invalid_address'] = 'כתובת דוא"ל לא תקנית: %s';
$lang['email_attachment_missing'] = ' הקובץ המצורף לדוא"ל לא ניתן לאיתור: %s';
$lang['email_attachment_unreadable'] = 'הקובץ המצורף הבא לא ניתן לפתיחה: %s';
$lang['email_no_from'] = 'לא ניתן לשלוח דואר ללא הערך "מאת".';
$lang['email_no_recipients'] = 'חובה לכלול נמען:: To, Cc, or Bcc';
$lang['email_send_failure_phpmail'] = 'לא ניתן לשלוח דוא"ל באמצעות PHP mail(). ייתכן שהשרת שלך לא מוגדר למשלוח דואר בשיטה זו.';
$lang['email_send_failure_sendmail'] = 'לא ניתן לשלוח דוא"ל באמצעות PHP Sendmail. ייתכן שהשרת שלך לא מוגדר למשלוח דואר בשיטה זו.';
$lang['email_send_failure_smtp'] = 'לא ניתן לשלוח דוא"ל באמצעות PHP SMTP. ייתכן שהשרת שלך לא מוגדר למשלוח דואר בשיטה זו.';
$lang['email_sent'] = 'ההודעה שלך נשלחה בהצלחה באמצעות הפרוטוקול הבא: %s';
$lang['email_no_socket'] = 'לא ניתן לפתוח socket ל- Sendmail. אנא בדוק הגדרות.';
$lang['email_no_hostname'] = 'לא ציינת שם  SMTP hostname.';
$lang['email_smtp_error'] = 'אירעה שגיאת ה- SMTP הבאה: %s';
$lang['email_no_smtp_unpw'] = 'שגיאה: עליך להקצות שם משתמש וסיסמה ל SMTP..';
$lang['email_failed_smtp_login'] = 'שליחת הפקודה AUTH LOGIN נכשלה. שגיאה: %s';
$lang['email_smtp_auth_un'] = 'אימות שם המשתמש נכשל. שגיאה: %s';
$lang['email_smtp_auth_pw'] = 'אימות הסיסמה נכשל. שגיאה: %s';
$lang['email_smtp_data_failure'] = 'לא ניתן לשלוח את הנתונים: %s';
$lang['email_exit_status'] = 'קוד סטטוס ליציאה: %s';

