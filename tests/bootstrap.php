<?php
/**
 * PHPUnit Bootstrap File
 *
 * Loads required components for testing without the full CodeIgniter framework.
 */

// Define BASEPATH to satisfy helper file guards
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load application helpers needed for tests
require_once APPPATH . 'helpers/array_helper.php';
require_once APPPATH . 'helpers/validation_helper.php';

// Minimal CodeIgniter stand-ins, so the permission helper can be tested without booting the framework.
define('DB_SLUG_PROVIDER', 'provider');
define('DB_SLUG_SECRETARY', 'secretary');

$GLOBALS['ea_test_session'] = [];
$GLOBALS['ea_test_records'] = [];
$GLOBALS['ea_test_secretary_providers'] = [];

function session(?string $key = null, $default = null)
{
    return $GLOBALS['ea_test_session'][$key] ?? $default;
}

function abort(int $code, string $message = ''): void
{
    throw new RuntimeException($message ?: (string) $code);
}

function &get_instance(): object
{
    static $instance;

    $instance ??= new class {
        public object $db;
        public object $load;
        public object $secretaries_model;

        public function __construct()
        {
            $this->db = new class {
                private int $id = 0;

                public function get_where(string $table, array $where): object
                {
                    $this->id = (int) $where['id'];

                    return $this;
                }

                public function row_array(): ?array
                {
                    return $GLOBALS['ea_test_records'][$this->id] ?? null;
                }
            };

            $this->load = new class {
                public function model(string $name): void
                {
                    // The models are already available on the stub instance.
                }
            };

            $this->secretaries_model = new class {
                public function is_provider_supported(int $secretary_id, int $provider_id): bool
                {
                    return in_array($provider_id, $GLOBALS['ea_test_secretary_providers'][$secretary_id] ?? []);
                }
            };
        }
    };

    return $instance;
}

require_once APPPATH . 'helpers/permission_helper.php';
