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

    // Use bcrypt for secure password hashing (salt parameter is kept for backward compatibility but not used)
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    if ($hash === false) {
        throw new RuntimeException('Password hashing failed.');
    }

    return $hash;
}

/**
 * Verify a password against a hash.
 *
 * This function supports both the new bcrypt hashes and legacy SHA256 hashes for backward compatibility.
 *
 * @param string $salt Salt value for the user (used for legacy hash verification).
 * @param string $password The plain text password to verify.
 * @param string $hash The stored hash to verify against.
 *
 * @return bool Returns true if the password matches the hash.
 */
function verify_password(string $salt, string $password, string $hash): bool
{
    if (strlen($password) > MAX_PASSWORD_LENGTH) {
        return false;
    }

    // Check if it's a bcrypt hash (starts with $2y$ or $2a$ or $2b$)
    if (preg_match('/^\$2[ayb]\$/', $hash)) {
        return password_verify($password, $hash);
    }

    // Legacy SHA256 verification for backward compatibility
    $half = (int) (strlen($salt) / 2);
    $legacy_hash = hash('sha256', substr($salt, 0, $half) . $password . substr($salt, $half));

    for ($i = 0; $i < 100000; $i++) {
        $legacy_hash = hash('sha256', $legacy_hash);
    }

    return hash_equals($legacy_hash, $hash);
}

/**
 * Check if a password hash needs to be rehashed (e.g., migrated from legacy to bcrypt).
 *
 * @param string $hash The stored hash to check.
 *
 * @return bool Returns true if the hash should be rehashed.
 */
function password_needs_rehash_check(string $hash): bool
{
    // If it's not a bcrypt hash, it needs rehashing
    if (!preg_match('/^\$2[ayb]\$/', $hash)) {
        return true;
    }

    // Check if bcrypt cost needs updating
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
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
