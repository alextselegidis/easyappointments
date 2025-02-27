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

$lang['email_must_be_array'] = 'Metoda e validimit të email-it duhet të pranojë një array.';
$lang['email_invalid_address'] = 'Adresa e email-it është e pavlefshme: %s';
$lang['email_attachment_missing'] = 'Nuk mund të gjendet bashkëngjitja e mëposhtme e email-it: %s';
$lang['email_attachment_unreadable'] = 'Nuk mund të hapet kjo bashkëngjitje: %s';
$lang['email_no_from'] = 'Nuk mund të dërgohet email-i pa një header "From".';
$lang['email_no_recipients'] = 'Duhet të përfshini marrësit: To, Cc, ose Bcc';
$lang['email_send_failure_phpmail'] = 'Nuk mund të dërgohet email duke përdorur PHP mail(). Serveri juaj mund të mos jetë konfiguruar për të dërguar email me këtë metodë.';
$lang['email_send_failure_sendmail'] = 'Nuk mund të dërgohet email duke përdorur PHP Sendmail. Serveri juaj mund të mos jetë konfiguruar për të dërguar email me këtë metodë.';
$lang['email_send_failure_smtp'] = 'Nuk mund të dërgohet email duke përdorur PHP SMTP. Serveri juaj mund të mos jetë konfiguruar për të dërguar email me këtë metodë.';
$lang['email_sent'] = 'Mesazhi juaj u dërgua me sukses duke përdorur protokollin e mëposhtëm: %s';
$lang['email_no_socket'] = 'Nuk mund të hapet një socket për Sendmail. Ju lutemi kontrolloni cilësimet.';
$lang['email_no_hostname'] = 'Nuk keni specifikuar një hostname për SMTP.';
$lang['email_smtp_error'] = 'U has gabimi i mëposhtëm i SMTP: %s';
$lang['email_no_smtp_unpw'] = 'Gabim: Duhet të caktoni një emër përdoruesi dhe fjalëkalim për SMTP.';
$lang['email_failed_smtp_login'] = 'Dështoi dërgimi i komandës AUTH LOGIN. Gabim: %s';
$lang['email_smtp_auth_un'] = 'Dështoi autentifikimi i emrit të përdoruesit. Gabim: %s';
$lang['email_smtp_auth_pw'] = 'Dështoi autentifikimi i fjalëkalimit. Gabim: %s';
$lang['email_smtp_data_failure'] = 'Nuk mund të dërgohen të dhënat: %s';
$lang['email_exit_status'] = 'Kodi i statusit të daljes: %s';
