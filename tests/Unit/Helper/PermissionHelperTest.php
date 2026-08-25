<?php

namespace Tests\Unit\Helper;

use RuntimeException;
use Tests\TestCase;

/**
 * Covers the provider scope of the appointment / unavailability write authorization.
 *
 * The helper must reject both sides of the provider boundary: the record that is about to be overwritten and the
 * provider the record will be assigned to.
 */
class PermissionHelperTest extends TestCase {
    protected function setUp(): void
    {
        // Provider 11 owns appointment 1207, provider 45 owns appointment 481.
        $GLOBALS['ea_test_records'] = [
            1207 => ['id' => 1207, 'id_users_provider' => 11],
            481 => ['id' => 481, 'id_users_provider' => 45],
        ];

        // Secretary 7 is only assigned to provider 11.
        $GLOBALS['ea_test_secretary_providers'] = [7 => [11]];
    }

    private function login(int $user_id, string $role_slug): void
    {
        $GLOBALS['ea_test_session'] = ['user_id' => $user_id, 'role_slug' => $role_slug];
    }

    public function testProviderCannotOverwriteAppointmentOfAnotherProvider(): void
    {
        $this->login(11, DB_SLUG_PROVIDER);

        $this->expectException(RuntimeException::class);

        // Provider 11 targets themselves, but appointment 481 belongs to provider 45.
        authorize_event_write(481, 11);
    }

    public function testProviderCannotReassignOwnAppointmentToAnotherProvider(): void
    {
        $this->login(11, DB_SLUG_PROVIDER);

        $this->expectException(RuntimeException::class);

        authorize_event_write(1207, 45);
    }

    public function testSecretaryCannotReassignAppointmentToUnsupportedProvider(): void
    {
        $this->login(7, DB_SLUG_SECRETARY);

        $this->expectException(RuntimeException::class);

        authorize_event_write(1207, 45);
    }

    public function testSecretaryCannotCreateAppointmentForUnsupportedProvider(): void
    {
        $this->login(7, DB_SLUG_SECRETARY);

        $this->expectException(RuntimeException::class);

        authorize_event_write(null, 45);
    }

    public function testMissingRecordIsRejected(): void
    {
        $this->login(11, DB_SLUG_PROVIDER);

        $this->expectException(RuntimeException::class);

        authorize_event_write(999999, 11);
    }

    public function testProviderCanUpdateOwnAppointment(): void
    {
        $this->login(11, DB_SLUG_PROVIDER);

        authorize_event_write(1207, 11);

        $this->expectNotToPerformAssertions();
    }

    public function testSecretaryCanUpdateAppointmentOfSupportedProvider(): void
    {
        $this->login(7, DB_SLUG_SECRETARY);

        authorize_event_write(1207, 11);

        $this->expectNotToPerformAssertions();
    }

    public function testAdminIsNotBoundToAProviderScope(): void
    {
        $this->login(1, 'admin');

        authorize_event_write(481, 45);

        $this->expectNotToPerformAssertions();
    }
}
