<?php

namespace Tests\Unit\Model;

use InvalidArgumentException;
use ReflectionClass;
use Service_categories_model;
use Tests\TestCase;

class ServiceCategoriesModelTest extends TestCase
{
    private function service_categories_model(): Service_categories_model
    {
        // EA_Model extends CodeIgniter's CI_Model, which must be defined before EA_Model is parsed.
        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'core/EA_Model.php';
        require_once APPPATH . 'models/Service_categories_model.php';

        // The constructor only loads CodeIgniter model/DB dependencies that validate() does not
        // need, as long as the "id" key (the only $this->db touch) is omitted from the payload.
        return (new ReflectionClass(Service_categories_model::class))->newInstanceWithoutConstructor();
    }

    public function testValidateThrowsWhenNameIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service_categories_model()->validate(['description' => 'Hair services']);
    }

    public function testValidatePassesForAFullyValidPayload(): void
    {
        $this->service_categories_model()->validate(['name' => 'Hair', 'description' => 'Hair services']);

        $this->assertTrue(true); // No exception thrown.
    }
}
