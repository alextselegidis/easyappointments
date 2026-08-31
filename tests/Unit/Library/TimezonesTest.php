<?php

namespace Tests\Unit\Library;

use DateTimeImmutable;
use DateTimeZone;
use ReflectionClass;
use Tests\TestCase;
use Timezones;

class TimezonesTest extends TestCase
{
    private function timezones(): Timezones
    {
        require_once APPPATH . 'libraries/Timezones.php';

        // The constructor only loads CodeIgniter dependencies that the timezone list does not need.
        return (new ReflectionClass(Timezones::class))->newInstanceWithoutConstructor();
    }

    public function testGroupedArrayContainsOnlyCurrentIdentifiers()
    {
        $grouped = $this->timezones()->to_grouped_array();

        $this->assertSame(['UTC' => 'UTC'], $grouped['UTC']);
        $this->assertArrayHasKey('Europe/Berlin', $grouped['Europe']);
        $this->assertArrayNotHasKey('Asia/Calcutta', $grouped['Asia']); // Deprecated alias of "Asia/Kolkata".
    }

    public function testLabelsContainThePaddedOffsetThatIsCurrentlyInEffect()
    {
        $timezones = $this->timezones()->to_array();

        foreach (['Europe/Berlin', 'America/St_Johns', 'Pacific/Marquesas', 'Asia/Kolkata'] as $identifier) {
            $offset = (new DateTimeZone($identifier))->getOffset(new DateTimeImmutable());

            $expected = sprintf(
                '(%s%02d:%02d)',
                $offset < 0 ? '-' : '+',
                intdiv(abs($offset), 3600),
                intdiv(abs($offset) % 3600, 60),
            );

            $this->assertStringContainsString($expected, $timezones[$identifier]);
            $this->assertStringNotContainsString('_', $timezones[$identifier]);
        }
    }

    public function testFlatArrayResolvesDeprecatedIdentifiers()
    {
        $timezones = $this->timezones();

        $this->assertSame('Calcutta (+05:30)', $timezones->get_timezone_name('Asia/Calcutta'));
        $this->assertNull($timezones->get_timezone_name('Not/AZone'));
    }
}
