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

    public function testValidatePassesForAFullyValidPayload(): void
    {
        $this->services_model()->validate(['name' => 'Haircut', 'attendants_number' => 1]);

        $this->assertTrue(true); // No exception thrown.
    }
}
