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

$lang['email_must_be_array'] = 'Metoda validace emailu musí být předána jako pole.';
$lang['email_invalid_address'] = 'Neplatná emailová adresa: %s';
$lang['email_attachment_missing'] = 'Není možné nalézt následující přílohu emailu: %s';
$lang['email_attachment_unreadable'] = 'Není možné otevřít tuto přílohu: %s';
$lang['email_no_from'] = 'Nelze odeslat email bez hodnoty pole "Od".';
$lang['email_no_recipients'] = 'Musíte zadat příjemce: Komu, Kopie nebo Skrytá kopie';
$lang['email_send_failure_phpmail'] = 'Není možné odeslat email pomocí PHP mail(). Váš server možná není konfigurován, aby odesílal emaily protřednictvím této metody.';
$lang['email_send_failure_sendmail'] = 'Není možné odeslat email pomocí PHP Sendmail. Váš server možná není konfigurován, aby odesílal emaily prostřednictvím této metody.';
$lang['email_send_failure_smtp'] = 'Není možné odeslat email pomocí PHP SMTP. Váš server možná není konfigurován, aby odesílal emaily prostřednictvím této metody.';
$lang['email_sent'] = 'Vaše zpráva byla úspěšně odeslána prostřednictvím následujícího protokolu: %s';
$lang['email_no_socket'] = 'Není možné otevřít socket pro Sendmail. Zkontrolujte prosím nastavení.';
$lang['email_no_hostname'] = 'Nezadali jste název hosta SMTP.';
$lang['email_smtp_error'] = 'Vyskytla se následující SMTP chyba: %s';
$lang['email_no_smtp_unpw'] = 'Chyba: Musíte přidělit uživatelské jméno a heslo SMTP.';
$lang['email_failed_smtp_login'] = 'Odeslání příkazu AUTH LOGIN selhalo. Chyba: %s';
$lang['email_smtp_auth_un'] = 'Autentizace uživatelského jména selhala. Chyba: %s';
$lang['email_smtp_auth_pw'] = 'Autentizace hesla selhala. Chyba: %s';
$lang['email_smtp_data_failure'] = 'Není možné odeslat data: %s';
$lang['email_exit_status'] = 'Výstupní kód ukončení programu: %s';
