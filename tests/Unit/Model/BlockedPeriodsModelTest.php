<?php

namespace Tests\Unit\Model;

use Blocked_periods_model;
use InvalidArgumentException;
use ReflectionClass;
use Tests\TestCase;

class BlockedPeriodsModelTest extends TestCase
{
    private function blocked_periods_model(): Blocked_periods_model
    {
        // EA_Model extends CodeIgniter's CI_Model, which must be defined before EA_Model is parsed.
        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'core/EA_Model.php';
        require_once APPPATH . 'models/Blocked_periods_model.php';

        // The constructor only loads CodeIgniter model/DB dependencies that validate() does not
        // need, as long as the "id" key (the only $this->db touch) is omitted from the payload.
        return (new ReflectionClass(Blocked_periods_model::class))->newInstanceWithoutConstructor();
    }

    public function testValidateThrowsWhenNameIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->blocked_periods_model()->validate([
            'start_datetime' => '2026-01-01 09:00:00',
            'end_datetime' => '2026-01-01 17:00:00',
        ]);
    }

    public function testValidateThrowsWhenStartDatetimeIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->blocked_periods_model()->validate([
            'name' => 'Holiday',
            'end_datetime' => '2026-01-01 17:00:00',
        ]);
    }

    public function testValidateThrowsWhenEndDatetimeIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->blocked_periods_model()->validate([
            'name' => 'Holiday',
            'start_datetime' => '2026-01-01 09:00:00',
        ]);
    }

    public function testValidateThrowsWhenStartIsAfterEnd(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->blocked_periods_model()->validate([
            'name' => 'Holiday',
            'start_datetime' => '2026-01-01 17:00:00',
            'end_datetime' => '2026-01-01 09:00:00',
        ]);
    }

    public function testValidateThrowsWhenStartEqualsEnd(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->blocked_periods_model()->validate([
            'name' => 'Holiday',
            'start_datetime' => '2026-01-01 09:00:00',
            'end_datetime' => '2026-01-01 09:00:00',
        ]);
    }

    public function testValidatePassesForAFullyValidPayload(): void
    {
        $this->blocked_periods_model()->validate([
            'name' => 'Holiday',
            'start_datetime' => '2026-01-01 09:00:00',
            'end_datetime' => '2026-01-01 17:00:00',
        ]);

        $this->assertTrue(true); // No exception thrown.
    }
}
