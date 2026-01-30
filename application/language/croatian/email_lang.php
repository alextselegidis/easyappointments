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

$lang['email_must_be_array'] = 'Metoda za provjeru e-pošte mora primiti polje.';
$lang['email_invalid_address'] = 'Neispravna adresa e-pošte: %s';
$lang['email_attachment_missing'] = 'Nije moguće pronaći sljedeći privitak e-pošte: %s';
$lang['email_attachment_unreadable'] = 'Nije moguće otvoriti ovaj privitak: %s';
$lang['email_no_from'] = 'Nije moguće poslati poštu bez "From" zaglavlja.';
$lang['email_no_recipients'] = 'Morate uključiti primatelje: Za, Cc ili Bcc';
$lang['email_send_failure_phpmail'] = 'Nije moguće poslati e-poštu koristeći PHP mail(). Vaš poslužitelj možda nije konfiguriran za slanje pošte ovom metodom.';
$lang['email_send_failure_sendmail'] = 'Nije moguće poslati e-poštu koristeći PHP Sendmail. Vaš poslužitelj možda nije konfiguriran za slanje pošte ovom metodom.';
$lang['email_send_failure_smtp'] = 'Nije moguće poslati e-poštu koristeći PHP SMTP. Vaš poslužitelj možda nije konfiguriran za slanje pošte ovom metodom.';
$lang['email_sent'] = 'Vaša poruka je uspješno poslana koristeći sljedeći protokol: %s';
$lang['email_no_socket'] = 'Nije moguće otvoriti socket za Sendmail. Molimo provjerite postavke.';
$lang['email_no_hostname'] = 'Niste naveli SMTP naziv poslužitelja.';
$lang['email_smtp_error'] = 'Pojavila se sljedeća SMTP pogreška: %s';
$lang['email_no_smtp_unpw'] = 'Pogreška: Morate dodijeliti SMTP korisničko ime i lozinku.';
$lang['email_failed_smtp_login'] = 'Slanje AUTH LOGIN naredbe nije uspjelo. Pogreška: %s';
$lang['email_smtp_auth_un'] = 'Neuspjela autentifikacija korisničkog imena. Pogreška: %s';
$lang['email_smtp_auth_pw'] = 'Neuspjela autentifikacija lozinke. Pogreška: %s';
$lang['email_smtp_data_failure'] = 'Nije moguće poslati podatke: %s';
$lang['email_exit_status'] = 'Izlazni statusni kod: %s';
