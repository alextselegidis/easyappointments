<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Generate a hash of password string.
 *
 * For user security, all system passwords are stored in hash string into the database. Use this method to produce the
 * hashed password.
 *
 * @param string $salt Salt value for current user. This value is stored on the database and is used when generating
 * the password hashes.
 * @param string $password Given string password.
 *
 * @return string Returns the hash string of the given password.
 *
 * @throws Exception
 */
function hash_password(string $salt, string $password): string
{
    if (strlen($password) > MAX_PASSWORD_LENGTH) {
        throw new InvalidArgumentException('The provided password is too long, please use a shorter value.');
    }

    $half = (int) (strlen($salt) / 2);

    $hash = hash('sha256', substr($salt, 0, $half) . $password . substr($salt, $half));

    for ($i = 0; $i < 100000; $i++) {
        $hash = hash('sha256', $hash);
    }

    return $hash;
}

/**
 * Generate a new password salt.
 *
 * This method will not check if the salt is unique in database. This must be done
 * from the calling procedure.
 *
 * @return string Returns a salt string.
 */
function generate_salt(): string
{
    $max_length = 100;

    $salt = hash('sha256', uniqid(rand(), true));

    return substr($salt, 0, $max_length);
}
