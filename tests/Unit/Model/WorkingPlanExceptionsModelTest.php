<?php

namespace Tests\Unit\Model;

use InvalidArgumentException;
use ReflectionClass;
use Tests\TestCase;
use Working_plan_exceptions_model;

class WorkingPlanExceptionsModelTest extends TestCase
{
    private function working_plan_exceptions_model(): Working_plan_exceptions_model
    {
        // EA_Model extends CodeIgniter's CI_Model, which must be defined before EA_Model is parsed.
        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'core/EA_Model.php';
        require_once APPPATH . 'models/Working_plan_exceptions_model.php';

        // The constructor only loads CodeIgniter model/DB dependencies that validate() does not
        // need, as long as the "id" key (the only $this->db touch) is omitted from the payload.
        return (new ReflectionClass(Working_plan_exceptions_model::class))->newInstanceWithoutConstructor();
    }

    private function valid_payload(array $overrides = []): array
    {
        return array_merge(
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-02',
                'id_users_provider' => 1,
            ],
            $overrides,
        );
    }

    public function testValidateThrowsWhenStartDateIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $payload = $this->valid_payload();
        unset($payload['start_date']);

        $this->working_plan_exceptions_model()->validate($payload);
    }

    public function testValidateThrowsWhenEndDateIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $payload = $this->valid_payload();
        unset($payload['end_date']);

        $this->working_plan_exceptions_model()->validate($payload);
    }

    public function testValidateThrowsWhenProviderIdIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $payload = $this->valid_payload();
        unset($payload['id_users_provider']);

        $this->working_plan_exceptions_model()->validate($payload);
    }

    public function testValidateThrowsWhenStartDateFormatIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->working_plan_exceptions_model()->validate($this->valid_payload(['start_date' => '01-01-2026']));
    }

    public function testValidateThrowsWhenEndDateFormatIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->working_plan_exceptions_model()->validate($this->valid_payload(['end_date' => '2026-13-40']));
    }

    public function testValidateThrowsWhenStartDateIsAfterEndDate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->working_plan_exceptions_model()->validate(
            $this->valid_payload(['start_date' => '2026-01-05', 'end_date' => '2026-01-01']),
        );
    }

    public function testValidatePassesWhenStartDateEqualsEndDate(): void
    {
        $this->working_plan_exceptions_model()->validate(
            $this->valid_payload(['start_date' => '2026-01-01', 'end_date' => '2026-01-01']),
        );

        $this->assertTrue(true); // No exception thrown.
    }

    public function testValidateThrowsWhenStartTimeIsAfterEndTime(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->working_plan_exceptions_model()->validate(
            $this->valid_payload(['start_time' => '17:00:00', 'end_time' => '09:00:00']),
        );
    }

    public function testValidatePassesWhenStartOrEndTimeOmitted(): void
    {
        $this->working_plan_exceptions_model()->validate($this->valid_payload(['start_time' => '17:00:00']));

        $this->assertTrue(true); // No exception thrown.
    }

    public function testValidatePassesForAFullyValidPayload(): void
    {
        $this->working_plan_exceptions_model()->validate(
            $this->valid_payload(['start_time' => '09:00:00', 'end_time' => '17:00:00']),
        );

        $this->assertTrue(true); // No exception thrown.
    }
}
