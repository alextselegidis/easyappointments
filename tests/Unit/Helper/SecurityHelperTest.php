<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class SecurityHelperTest extends TestCase
{
    public function testRemoveUntouchedSensitiveSettingsDropsEmptySensitiveValues(): void
    {
        $settings = [
            ['name' => 'google_sync_feature', 'value' => '1'],
            ['name' => 'google_client_id', 'value' => ''],
            ['name' => 'google_client_secret', 'value' => ''],
            ['name' => 'ldap_password', 'value' => ''],
            ['name' => 'api_token', 'value' => ''],
        ];

        $this->assertSame(
            [['name' => 'google_sync_feature', 'value' => '1'], ['name' => 'google_client_id', 'value' => '']],
            remove_untouched_sensitive_settings($settings),
        );
    }

    public function testRemoveUntouchedSensitiveSettingsKeepsNewSensitiveValues(): void
    {
        $settings = [
            ['name' => 'google_client_secret', 'value' => 'new-secret'],
            ['name' => 'ldap_password', 'value' => '0'],
        ];

        $this->assertSame($settings, remove_untouched_sensitive_settings($settings));
    }

    public function testFilterSensitiveSettingsHidesEverySensitiveSetting(): void
    {
        $settings = array_map(
            fn($name) => ['name' => $name, 'value' => 'stored'],
            array_merge(sensitive_setting_names(), ['company_name']),
        );

        $this->assertSame([['name' => 'company_name', 'value' => 'stored']], filter_sensitive_settings($settings));
    }
}
