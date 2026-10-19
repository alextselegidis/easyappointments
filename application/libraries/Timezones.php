<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Timezones library.
 *
 * Handles timezone related functionality.
 *
 * @package Libraries
 */
class Timezones
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * @var string
     */
    protected string $default = 'UTC';

    /**
     * @var array|null
     */
    protected ?array $timezones = null;

    /**
     * @var array|null
     */
    protected ?array $flat_timezones = null;

    /**
     * Timezones constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('users_model');
    }

    /**
     * Get all timezones to a grouped array (by continent).
     *
     * @return array
     */
    public function to_grouped_array(): array
    {
        if ($this->timezones === null) {
            $this->timezones = $this->generate_timezones();
        }

        return $this->timezones;
    }

    /**
     * Build the timezone list of the system, grouped by area.
     *
     * Labels display the offset that is currently in effect, so that they do not contradict the date time conversions
     * of the app during daylight saving time.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function generate_timezones(): array
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $timezones = [
            'UTC' => [
                'UTC' => 'UTC',
            ],
        ];

        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            if (!str_contains($identifier, '/')) {
                continue; // Only the "UTC" identifier has no area prefix and it is already in the list.
            }

            [$area] = explode('/', $identifier, 2);

            $timezones[$area][$identifier] = $this->generate_timezone_label($identifier, $now);
        }

        return $timezones;
    }

    /**
     * Generate the display label of a timezone identifier.
     *
     * @param string $identifier Timezone identifier (e.g. "Europe/Berlin").
     * @param DateTimeInterface $moment Calculate the offset that is in effect at this moment.
     *
     * @return string
     */
    protected function generate_timezone_label(string $identifier, DateTimeInterface $moment): string
    {
        $location = str_contains($identifier, '/') ? explode('/', $identifier, 2)[1] : $identifier;

        $offset = (new DateTimeZone($identifier))->getOffset($moment);

        return sprintf(
            '%s (%s%02d:%02d)',
            str_replace(['_', '/'], [' ', ' - '], $location),
            $offset < 0 ? '-' : '+',
            intdiv(abs($offset), 3600),
            intdiv(abs($offset) % 3600, 60),
        );
    }

    /**
     * Get the default timezone value of the current system.
     *
     * @return string
     */
    public function get_default_timezone(): string
    {
        return date_default_timezone_get();
    }

    /**
     * Convert a date time value to a new timezone.
     *
     * @param string $value Provide a date time value as a string (format Y-m-d H:i:s).
     * @param string $from_timezone From timezone value.
     * @param string $to_timezone To timezone value.
     *
     * @return string
     *
     * @throws Exception
     */
    public function convert(string $value, string $from_timezone, string $to_timezone): string
    {
        if (!$to_timezone || $from_timezone === $to_timezone) {
            return $value;
        }

        $from = new DateTimeZone($from_timezone);

        $to = new DateTimeZone($to_timezone);

        $result = new DateTime($value, $from);

        $result->setTimezone($to);

        return $result->format('Y-m-d H:i:s');
    }

    /**
     * Get the timezone name for the provided value.
     *
     * @param string $value
     *
     * @return string|null
     */
    public function get_timezone_name(string $value): ?string
    {
        $timezones = $this->to_array();

        return $timezones[$value] ?? null;
    }

    /**
     * Get all timezones to a flat array.
     *
     * @return array
     *
     * @throws Exception
     */
    public function to_array(): array
    {
        if ($this->flat_timezones === null) {
            $flat_timezones = array_merge(...array_values($this->to_grouped_array()));

            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

            // Deprecated identifiers (e.g. "Asia/Calcutta") are not offered for selection anymore, but existing
            // records may still hold one of them, so they must resolve to a label as well.
            foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC) as $identifier) {
                if (isset($flat_timezones[$identifier])) {
                    continue;
                }

                try {
                    $flat_timezones[$identifier] = $this->generate_timezone_label($identifier, $now);
                } catch (Throwable) {
                    // The list also contains entries that are no timezones at all (e.g. "leapseconds").
                }
            }

            $this->flat_timezones = $flat_timezones;
        }

        return $this->flat_timezones;
    }
}
