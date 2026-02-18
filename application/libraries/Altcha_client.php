<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;

/**
 * Altcha_client library.
 *
 * Handles ALTCHA challenge generation and verification.
 *
 * @package Libraries
 */
class Altcha_client
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Altcha_client constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Check if ALTCHA is enabled.
     *
     * @return bool
     */
    public function is_enabled(): bool
    {
        return setting('altcha_enabled') === '1';
    }

    /**
     * Get the HMAC key for ALTCHA.
     *
     * @return string
     */
    private function get_hmac_key(): string
    {
        $hmac_key = setting('altcha_hmac_key');

        if (empty($hmac_key)) {
            // Generate a default key if not set
            $hmac_key = bin2hex(random_bytes(32));
        }

        return $hmac_key;
    }

    /**
     * Create an ALTCHA challenge.
     *
     * @return array Challenge data to send to frontend.
     */
    public function create_challenge(): array
    {
        $hmac_key = $this->get_hmac_key();
        $max_number = (int) setting('altcha_max_number') ?: 100000;
        $expires_seconds = (int) setting('altcha_expires') ?: 300;

        $altcha = new Altcha($hmac_key);

        $options = new ChallengeOptions(
            algorithm: Algorithm::SHA256,
            maxNumber: $max_number,
            expires: new DateTime('+' . $expires_seconds . ' seconds'),
        );

        $challenge = $altcha->createChallenge($options);

        return [
            'algorithm' => $challenge->algorithm,
            'challenge' => $challenge->challenge,
            'maxnumber' => $challenge->maxNumber,
            'salt' => $challenge->salt,
            'signature' => $challenge->signature,
        ];
    }

    /**
     * Verify an ALTCHA solution.
     *
     * @param string $payload Base64-encoded JSON payload from frontend.
     *
     * @return bool True if valid, false otherwise.
     */
    public function verify(string $payload): bool
    {
        if (empty($payload)) {
            return false;
        }

        $hmac_key = $this->get_hmac_key();

        try {
            $altcha = new Altcha($hmac_key);

            return $altcha->verifySolution($payload, true);
        } catch (Exception $e) {
            log_message('error', 'ALTCHA verification failed: ' . $e->getMessage());

            return false;
        }
    }
}
