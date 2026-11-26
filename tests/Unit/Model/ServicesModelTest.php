<?php

namespace Tests\Unit\Model;

use InvalidArgumentException;
use ReflectionClass;
use Services_model;
use Tests\TestCase;

class ServicesModelTest extends TestCase
{
    private function services_model(): Services_model
    {
        // EA_Model extends CodeIgniter's CI_Model, which must be defined before EA_Model is parsed.
        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'core/EA_Model.php';
        require_once APPPATH . 'models/Services_model.php';

        // Matches application/config/constants.php -- defined here rather than requiring that
        // file wholesale, which would clash with the DB_SLUG_* stand-ins tests/bootstrap.php
        // already defines for the permission helper tests.
        defined('EVENT_MINIMUM_DURATION') or define('EVENT_MINIMUM_DURATION', 5);

        // The constructor only loads CodeIgniter model/DB dependencies that validate() does not
        // need, as long as the "id"/"id_service_categories" keys (the only $this->db touches) are
        // omitted from the payload.
        return (new ReflectionClass(Services_model::class))->newInstanceWithoutConstructor();
    }

    public function testValidateThrowsWhenNameIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['attendants_number' => 1]);
    }

    public function testValidateThrowsWhenDurationIsBelowTheMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['name' => 'Haircut', 'duration' => 2, 'attendants_number' => 1]);
    }

    public function testValidateThrowsWhenPriceIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['name' => 'Haircut', 'price' => -5, 'attendants_number' => 1]);
    }

    public function testValidateThrowsWhenSlotIntervalIsBelowOne(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['name' => 'Haircut', 'slot_interval' => 0, 'attendants_number' => 1]);
    }

    public function testValidateThrowsWhenAttendantsNumberIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['name' => 'Haircut']);
    }

    public function testValidateThrowsWhenAttendantsNumberIsBelowOne(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate(['name' => 'Haircut', 'attendants_number' => 0]);
    }

    public function testValidateThrowsWhenColorIsNotAHexValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate([
            'name' => 'Haircut',
            'color' => 'red" onmouseover="x',
            'attendants_number' => 1,
        ]);
    }

    public function testValidateThrowsWhenAProviderAttendantsNumberIsBelowOne(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->services_model()->validate([
            'name' => 'Yoga',
            'attendants_number' => 10,
            'provider_attendants_numbers' => [5 => '0'],
        ]);
    }

    public function testValidatePassesForTheGroupBookingOptions(): void
    {
        $this->services_model()->validate([
            'name' => 'Yoga',
            'attendants_number' => 10,
            'provider_attendants_numbers' => [5 => '6', 7 => ''],
        ]);

        $this->assertTrue(true); // No exception thrown.
    }

    public function testValidatePassesForAFullyValidPayload(): void
    {
        $this->services_model()->validate(['name' => 'Haircut', 'attendants_number' => 1]);

        $this->assertTrue(true); // No exception thrown.
    }

    public function testApiEncodeExposesTheProviderAttendantsNumbersAsAnObject(): void
    {
        $service = [
            'id' => 1,
            'name' => 'Yoga',
            'duration' => 60,
            'price' => 0,
            'currency' => 'EUR',
            'description' => null,
            'location' => null,
            'slot_interval' => 15,
            'attendants_number' => 10,
            'is_private' => false,
            'id_service_categories' => null,
            'provider_attendants_numbers' => [2 => 6],
        ];

        $this->services_model()->api_encode($service);

        $this->assertSame('{"2":6}', json_encode($service['providerAttendantsNumbers']));
    }

    public function testApiDecodeReadsTheProviderAttendantsNumbers(): void
    {
        $service = ['providerAttendantsNumbers' => ['2' => 6, '3' => null]];

        $this->services_model()->api_decode($service);

        $this->assertSame([2 => 6, 3 => null], $service['provider_attendants_numbers']);
    }
}
