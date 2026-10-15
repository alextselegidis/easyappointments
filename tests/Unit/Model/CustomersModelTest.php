<?php

namespace Tests\Unit\Model;

use Customers_model;
use InvalidArgumentException;
use Tests\TestCase;

class CustomersModelTest extends TestCase
{
    private const CUSTOMER_ROLE_ID = 3;

    private object $db;

    private function customers_model(array $users): Customers_model
    {
        defined('DB_SLUG_CUSTOMER') or define('DB_SLUG_CUSTOMER', 'customer');

        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'core/EA_Model.php';
        require_once APPPATH . 'models/Customers_model.php';

        // Minimal stand-in for the query builder calls made by find() and delete().
        $this->db = new class ($users, self::CUSTOMER_ROLE_ID) {
            public array $deleted = [];

            public function __construct(private array $users, private int $customer_role_id) {}

            public function get_where(string $table, array $where): object
            {
                $rows =
                    $table === 'roles'
                        ? [['id' => $this->customer_role_id, 'slug' => DB_SLUG_CUSTOMER]]
                        : array_filter(
                            $this->users,
                            fn($user) => array_intersect_assoc($where, $user) == $where,
                        );

                return new class (array_values($rows)) {
                    public function __construct(private array $rows) {}

                    public function row_array(): ?array
                    {
                        return $this->rows[0] ?? null;
                    }
                };
            }

            public function delete(string $table, array $where): void
            {
                $this->deleted[] = $where;
            }
        };

        // Skip the constructor, which loads CodeIgniter dependencies, and declare $db explicitly.
        $model = new class extends Customers_model {
            public $db;

            public function __construct() {}
        };
        $model->db = $this->db;

        return $model;
    }

    public function testFindThrowsForNonCustomerUser(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->customers_model([['id' => 1, 'id_roles' => 1]])->find(1);
    }

    public function testDeleteThrowsForNonCustomerUserAndDeletesNothing(): void
    {
        $model = $this->customers_model([['id' => 1, 'id_roles' => 1, 'email' => 'admin@example.org']]);

        try {
            $model->delete(1);
            $this->fail('Expected InvalidArgumentException.');
        } catch (InvalidArgumentException) {
            $this->assertSame([], $this->db->deleted);
        }
    }
}
