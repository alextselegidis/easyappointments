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

$lang['email_must_be_array'] = 'Sähköpostin validointimetodi tulee antaa taulukkona.';
$lang['email_invalid_address'] = 'Virheellinen sähköpostiosoite: %s';
$lang['email_attachment_missing'] = 'Tätä sähköpostiliitettä ei paikannettu: %s';
$lang['email_attachment_unreadable'] = 'Tätä sähköpostiliitettä ei voitu avata: %s';
$lang['email_no_from'] = 'Ei voi lähettää sähköpostia ilman "From" tunnistetta.';
$lang['email_no_recipients'] = 'Sinun on lisättävä vastaanottajat: To, Cc, tai Bcc';
$lang['email_send_failure_phpmail'] = 'Ei voitu lähettää sähköpostia käyttäen PHP mail() -komentoa. Palvelinta ei ehkä ole määritelty käyttämään sitä.';
$lang['email_send_failure_sendmail'] = 'Ei voitu lähettää sähköpostia käyttäen PHP Sendmail -komentoa. Palvelinta ei ehkä ole määritelty käyttämään sitä.';
$lang['email_send_failure_smtp'] = 'Ei voitu lähettää sähköpostia käyttäen PHP SMTP -ominaisuutta. Palvelinta ei ehkä ole määritelty käyttämään sitä.';
$lang['email_sent'] = 'Viestisi on lähetetty käyttäen protokollaa %s';
$lang['email_no_socket'] = 'Ei voitu avata socketia Sendmailille. Tarkista asetukset.';
$lang['email_no_hostname'] = 'Et ole määrittänyt SMTP -palvelinta.';
$lang['email_smtp_error'] = 'SMTP virhe tapahtui: %s';
$lang['email_no_smtp_unpw'] = 'Virhe: SMTP käyttäjänimi ja sanasana on määritettävä.';
$lang['email_failed_smtp_login'] = 'Ei voitu lähettää AUTH LOGIN komentoa. Virhe: %s';
$lang['email_smtp_auth_un'] = 'Ei voitu autentikoida käyttäjänimeä. Virhe: %s';
$lang['email_smtp_auth_pw'] = 'Ei voitu autentikoida salasanaa. Virhe: %s';
$lang['email_smtp_data_failure'] = 'Ei voitu lähettää dataa: %s';
$lang['email_exit_status'] = 'Paluukoodi: %s';
