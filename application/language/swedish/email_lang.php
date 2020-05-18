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

$lang['email_must_be_array'] = 'Epost-valideringsmetoden kräver en array.';
$lang['email_invalid_address'] = 'Ogiltig epostadress: %s';
$lang['email_attachment_missing'] = 'Hittar inte följande bifogade fil: %s';
$lang['email_attachment_unreadable'] = 'Kan inte öppna denna bifogade fil: %s';
$lang['email_no_from'] = 'Kan inte skicka epost utan "From"-header.';
$lang['email_no_recipients'] = 'Du måste ange mottagare: To, Cc, eller Bcc';
$lang['email_send_failure_phpmail'] = 'Kan inte skicka epost med PHP mail(). Din server är kanske inte konfigurerad för att skicka epost på detta sätt.';
$lang['email_send_failure_sendmail'] = 'Kan inte skicka epost med PHP Sendmail. Din server är kanske inte konfigurerad för att skicka epost på detta sätt.';
$lang['email_send_failure_smtp'] = 'Kan inte skicka epost med PHP SMTP. Din server är kanske inte konfigurerad för att skicka epost på detta sätt.';
$lang['email_sent'] = 'Ditt meddelande blev skickat med följande protokoll: %s';
$lang['email_no_socket'] = 'Kan inte ansluta till Sendmail. Kontrollera inställningarna.';
$lang['email_no_hostname'] = 'Du har inte angivit värdnamn för SMTP.';
$lang['email_smtp_error'] = 'Följande SMTP-fel uppstod: %s';
$lang['email_no_smtp_unpw'] = 'Fel: Du ska ange ett SMTP-användarnamn och -lösenord.';
$lang['email_failed_smtp_login'] = 'Lyckades inte skicka AUTH LOGIN kommando. Fel: %s';
$lang['email_smtp_auth_un'] = 'Fel vid auteticiering av användarnamn. Fel: %s';
$lang['email_smtp_auth_pw'] = 'Fel vid auteticiering av lösenord. Fel: %s';
$lang['email_smtp_data_failure'] = 'Kunde inte skicka data: %s';
$lang['email_exit_status'] = 'Statuskod vid avslut: %s';
