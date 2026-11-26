<?php

namespace Tests\Unit\Library;

use Availability;
use DateTime;
use ReflectionClass;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    private function availability(): Availability
    {
        require_once APPPATH . 'libraries/Availability.php';

        // The constructor only loads CodeIgniter model dependencies that these pure methods do not need.
        return (new ReflectionClass(Availability::class))->newInstanceWithoutConstructor();
    }

    private function period(string $date, string $start, string $end): array
    {
        return [
            'start' => new DateTime($date . ' ' . $start),
            'end' => new DateTime($date . ' ' . $end),
        ];
    }

    private function generate_available_hours(string $date, array $service, array $empty_periods): array
    {
        $method = (new ReflectionClass(Availability::class))->getMethod('generate_available_hours');

        $method->setAccessible(true);

        return $method->invoke($this->availability(), $date, $service, $empty_periods);
    }

    // ── remove_breaks() ──────────────────────────────────────────────────

    public function testRemoveBreaksTrimsTheLeftEdgeOfAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $breaks = [['start' => '08:00', 'end' => '09:30']];

        $result = $this->availability()->remove_breaks('2026-08-26', $periods, $breaks);

        $this->assertCount(1, $result);
        $this->assertSame('09:30', $result[0]['start']->format('H:i'));
        $this->assertSame('17:00', $result[0]['end']->format('H:i'));
    }

    public function testRemoveBreaksSplitsAPeriodAroundAMiddleBreak(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $breaks = [['start' => '12:00', 'end' => '13:00']];

        $result = $this->availability()->remove_breaks('2026-08-26', $periods, $breaks);

        $this->assertCount(2, $result);
        $this->assertSame('09:00', $result[0]['start']->format('H:i'));
        $this->assertSame('12:00', $result[0]['end']->format('H:i'));
        $this->assertSame('13:00', $result[1]['start']->format('H:i'));
        $this->assertSame('17:00', $result[1]['end']->format('H:i'));
    }

    public function testRemoveBreaksTrimsTheRightEdgeOfAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $breaks = [['start' => '16:30', 'end' => '18:00']];

        $result = $this->availability()->remove_breaks('2026-08-26', $periods, $breaks);

        $this->assertCount(1, $result);
        $this->assertSame('09:00', $result[0]['start']->format('H:i'));
        $this->assertSame('16:30', $result[0]['end']->format('H:i'));
    }

    public function testRemoveBreaksThatEntirelyContainsAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $breaks = [['start' => '08:00', 'end' => '18:00']];

        $result = $this->availability()->remove_breaks('2026-08-26', $periods, $breaks);

        // Only the period's start is moved to the break's end, so the result is a degenerate,
        // inverted period (start after end) rather than an empty one -- existing behaviour.
        $this->assertCount(1, $result);
        $this->assertSame('18:00', $result[0]['start']->format('H:i'));
        $this->assertSame('17:00', $result[0]['end']->format('H:i'));
    }

    // ── remove_unavailability_events() ─────────────────────────────────────

    public function testRemoveUnavailabilityEventsTrimsTheLeftEdgeOfAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $events = [['start_datetime' => '2026-08-26 08:00:00', 'end_datetime' => '2026-08-26 09:30:00']];

        $result = $this->availability()->remove_unavailability_events($periods, $events);

        $this->assertCount(1, $result);
        $this->assertSame('09:30', $result[0]['start']->format('H:i'));
        $this->assertSame('17:00', $result[0]['end']->format('H:i'));
    }

    public function testRemoveUnavailabilityEventsSplitsAPeriodAroundAMiddleEvent(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $events = [['start_datetime' => '2026-08-26 12:00:00', 'end_datetime' => '2026-08-26 13:00:00']];

        $result = $this->availability()->remove_unavailability_events($periods, $events);

        $this->assertCount(2, $result);
        $this->assertSame('09:00', $result[0]['start']->format('H:i'));
        $this->assertSame('12:00', $result[0]['end']->format('H:i'));
        $this->assertSame('13:00', $result[1]['start']->format('H:i'));
        $this->assertSame('17:00', $result[1]['end']->format('H:i'));
    }

    public function testRemoveUnavailabilityEventsTrimsTheRightEdgeOfAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $events = [['start_datetime' => '2026-08-26 16:30:00', 'end_datetime' => '2026-08-26 18:00:00']];

        $result = $this->availability()->remove_unavailability_events($periods, $events);

        $this->assertCount(1, $result);
        $this->assertSame('09:00', $result[0]['start']->format('H:i'));
        $this->assertSame('16:30', $result[0]['end']->format('H:i'));
    }

    public function testRemoveUnavailabilityEventThatEntirelyContainsAPeriod(): void
    {
        $periods = [$this->period('2026-08-26', '09:00', '17:00')];
        $events = [['start_datetime' => '2026-08-26 08:00:00', 'end_datetime' => '2026-08-26 18:00:00']];

        $result = $this->availability()->remove_unavailability_events($periods, $events);

        // Same existing behaviour as the equivalent remove_breaks() case: only the period's start
        // moves, leaving a degenerate, inverted period rather than an empty one.
        $this->assertCount(1, $result);
        $this->assertSame('18:00', $result[0]['start']->format('H:i'));
        $this->assertSame('17:00', $result[0]['end']->format('H:i'));
    }

    // ── generate_available_hours() ───────────────────────────────────────

    public function testGenerateAvailableHoursForANormalWorkingDay(): void
    {
        $service = ['duration' => 30, 'slot_interval' => 30];
        $periods = [['start' => '09:00', 'end' => '17:00']];

        $result = $this->generate_available_hours('2026-08-26', $service, $periods);

        $this->assertCount(16, $result);
        $this->assertSame('09:00', $result[0]);
        $this->assertSame('16:30', $result[array_key_last($result)]);
    }

    public function testGenerateAvailableHoursSkipsOverAnAlreadyRemovedBreak(): void
    {
        $service = ['duration' => 60, 'slot_interval' => 60];
        $periods = [['start' => '09:00', 'end' => '12:00'], ['start' => '13:00', 'end' => '17:00']];

        $result = $this->generate_available_hours('2026-08-26', $service, $periods);

        $this->assertSame(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'], $result);
        $this->assertNotContains('12:00', $result);
    }

    public function testGenerateAvailableHoursWithASlotIntervalThatDoesNotEvenlyDividePeriod(): void
    {
        $service = ['duration' => 20, 'slot_interval' => 25];
        $periods = [['start' => '09:00', 'end' => '10:00']];

        $result = $this->generate_available_hours('2026-08-26', $service, $periods);

        // 09:00 -> 09:25 -> 09:50 would only leave 10 minutes, less than the 20-minute duration.
        $this->assertSame(['09:00', '09:25'], $result);
    }

    // ── multiple attendants ──────────────────────────────────────────────

    /**
     * Build the library on top of stubbed models, for a provider that works from 09:00 to 11:00.
     *
     * @param array $booked_places Places that are already taken, keyed by the start hour (H:i) of the slot.
     * @param int|null $provider_attendants_number Attendants number that is set for the provider of the service.
     */
    private function availability_with_models(
        array $booked_places,
        ?int $provider_attendants_number = null,
    ): Availability {
        // Stand-in for the CodeIgniter controller, so the library can be handed stubbed models without booting the
        // framework. It is declared at runtime, as a class file in the tests directory would have to be namespaced.
        if (!class_exists('CI_Controller', false)) {
            eval('#[AllowDynamicProperties] class CI_Controller {}');
        }

        $CI = new \CI_Controller();

        $CI->blocked_periods_model = new class {
            public function is_entire_date_blocked(string $date): bool
            {
                return false;
            }

            public function get_for_period(string $start, string $end): array
            {
                return [];
            }
        };

        $CI->unavailabilities_model = new class {
            public function get(array $where): array
            {
                return [];
            }
        };

        $CI->working_plan_exceptions_model = new class {
            public function get_by_provider(int $provider_id): array
            {
                return [];
            }
        };

        $CI->services_model = new class ($provider_attendants_number) {
            public function __construct(private ?int $provider_attendants_number) {}

            public function get_attendants_number_for_provider(array $service, int $provider_id): int
            {
                return $this->provider_attendants_number ?? (int) $service['attendants_number'];
            }
        };

        $CI->appointments_model = new class ($booked_places) {
            public function __construct(private array $booked_places) {}

            public function get_other_service_attendants_number(DateTime $start): int
            {
                return 0;
            }

            public function get_attendants_number_for_period(DateTime $start): int
            {
                return $this->booked_places[$start->format('H:i')] ?? 0;
            }
        };

        $availability = $this->availability();

        $property = (new ReflectionClass(Availability::class))->getProperty('CI');

        $property->setAccessible(true);

        $property->setValue($availability, $CI);

        return $availability;
    }

    private function group_service(int $attendants_number): array
    {
        return ['id' => 1, 'duration' => 60, 'slot_interval' => 60, 'attendants_number' => $attendants_number];
    }

    private function provider(): array
    {
        $day = ['start' => '09:00', 'end' => '11:00', 'breaks' => []];

        $working_plan = array_fill_keys(
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            $day,
        );

        return ['id' => 2, 'timezone' => 'UTC', 'settings' => ['working_plan' => json_encode($working_plan)]];
    }

    private function tomorrow(): string
    {
        return (new DateTime('tomorrow'))->format('Y-m-d');
    }

    public function testGetAvailableHoursLeavesOutTheFullyBookedHours(): void
    {
        $availability = $this->availability_with_models(['09:00' => 10]);

        $result = $availability->get_available_hours($this->tomorrow(), $this->group_service(10), $this->provider());

        $this->assertSame(['10:00'], $result);
    }

    public function testGetAvailableHoursUsesTheAttendantsNumberOfTheProvider(): void
    {
        $availability = $this->availability_with_models(['09:00' => 4, '10:00' => 6], 6);

        $result = $availability->get_available_hours($this->tomorrow(), $this->group_service(10), $this->provider());

        // The provider only accepts 6 attendants, although the service is set to 10.
        $this->assertSame(['09:00'], $result);
    }
}
