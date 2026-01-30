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

$lang['email_must_be_array'] = 'E-postvalideringsmetoden må sendes til en matrise.';
$lang['email_invalid_address'] = 'Ugyldig e-postadresse: %s';
$lang['email_attachment_missing'] = 'Kunne ikke finne følgende e-postvedlegg: %s';
$lang['email_attachment_unreadable'] = 'Kan ikke åpne dette vedlegget: %s';
$lang['email_no_from'] = 'Kan ikke sende e-post uten «From»-overskrift.';
$lang['email_no_recipients'] = 'Du må inkludere mottakere: To, Cc eller Bcc';
$lang['email_send_failure_phpmail'] = 'Kunne ikke sende e-post ved hjelp av PHP mail(). Serveren din er kanskje ikke konfigurert til å sende e-post ved hjelp av denne metoden.';
$lang['email_send_failure_sendmail'] = 'Kunne ikke sende e-post ved hjelp av PHP Sendmail. Serveren din er kanskje ikke konfigurert til å sende e-post ved hjelp av denne metoden.';
$lang['email_send_failure_smtp'] = 'Kunne ikke sende e-post ved hjelp av PHP SMTP. Serveren din er kanskje ikke konfigurert til å sende e-post ved hjelp av denne metoden.';
$lang['email_sent'] = 'Meldingen din har blitt sendt ved hjelp av følgende protokoll: %s';
$lang['email_no_socket'] = 'Kunne ikke åpne en socket til Sendmail. Vennligst sjekk innstillingene.';
$lang['email_no_hostname'] = 'Du har ikke angitt et SMTP-vertsnavn.';
$lang['email_smtp_error'] = 'Følgende SMTP-feil ble oppdaget: %s';
$lang['email_no_smtp_unpw'] = 'Feil: Du må tilordne et SMTP-brukernavn og passord.';
$lang['email_failed_smtp_login'] = 'Kunne ikke sende AUTH LOGIN-kommandoen. Feil: %s';
$lang['email_smtp_auth_un'] = 'Kunne ikke autentisere brukernavn. Feil: %s';
$lang['email_smtp_auth_pw'] = 'Kunne ikke godkjenne passord. Feil: %s';
$lang['email_smtp_data_failure'] = 'Kunne ikke sende data: %s';
$lang['email_exit_status'] = 'Avslutt statuskode: %s';
