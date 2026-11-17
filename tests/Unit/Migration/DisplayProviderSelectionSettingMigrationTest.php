<?php

namespace Tests\Unit\Migration;

use Tests\TestCase;

class DisplayProviderSelectionSettingMigrationTest extends TestCase
{
    private function migration(array $existing_settings = []): object
    {
        if (!class_exists('EA_Migration', false)) {
            eval('class EA_Migration { public $db; }');
        }

        require_once APPPATH . 'migrations/070_add_display_provider_selection_setting.php';

        $migration = new \Migration_Add_display_provider_selection_setting();

        $migration->db = new class($existing_settings) {
            public array $inserted = [];
            public array $deleted = [];

            public function __construct(private array $existing)
            {
            }

            public function get_where(string $table, array $where): object
            {
                $count = in_array($where['name'], $this->existing, true) ? 1 : 0;

                return new class($count) {
                    public function __construct(private int $count)
                    {
                    }

                    public function num_rows(): int
                    {
                        return $this->count;
                    }
                };
            }

            public function insert(string $table, array $data): void
            {
                $this->inserted[] = [$table, $data];
            }

            public function delete(string $table, array $where): void
            {
                $this->deleted[] = [$table, $where];
            }
        };

        return $migration;
    }

    public function testUpEnablesProviderSelectionByDefault()
    {
        $migration = $this->migration();

        $migration->up();

        $this->assertSame(
            [['settings', ['name' => 'display_provider_selection', 'value' => '1']]],
            $migration->db->inserted,
        );
    }

    public function testUpDoesNotOverwriteAnExistingSetting()
    {
        $migration = $this->migration(['display_provider_selection']);

        $migration->up();

        $this->assertSame([], $migration->db->inserted);
    }

    public function testDownRemovesTheSetting()
    {
        $migration = $this->migration(['display_provider_selection']);

        $migration->down();

        $this->assertSame([['settings', ['name' => 'display_provider_selection']]], $migration->db->deleted);
    }

    public function testEnglishTranslationsExistForTheSetting()
    {
        $lang = [];

        require APPPATH . 'language/english/translations_lang.php';

        $this->assertNotEmpty($lang['display_provider_selection']);
        $this->assertNotEmpty($lang['display_provider_selection_hint']);
    }
}
