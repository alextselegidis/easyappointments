<?php

namespace Jsvrcek\ICS\Tests\Utility;

use Jsvrcek\ICS\Utility\Provider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\Utility\Provider::first
     */
    public function testManualFirst()
    {
        $provider = new Provider();
        $provider->add('one');
        $provider->add('two');
        $provider->add('three');

        $expected = 'one';
        $actual = $provider->first();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Utility\Provider::next
     * @covers Jsvrcek\ICS\Utility\Provider::current
     */
    public function testManualIterate()
    {
        $provider = new Provider();
        $provider->add('one');
        $provider->add('two');
        $provider->add('three');

        $expected = ['one', 'two', 'three'];
        $actual = [];
        foreach ($provider as $item) {
            $actual[] = $item;
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Utility\Provider::first
     */
    public function testClosureFirst()
    {
        $provider = new Provider(function($index) {
            switch ($index) {
                case 0: return ['one'];
                case 1: return ['two'];
                case 2: return ['three'];
                default: return [];
            }
        });

        $expected = 'one';
        $actual = $provider->first();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure closure is not called more than once when requesting
     * 
     * @covers Jsvrcek\ICS\Utility\Provider::first
     */
    public function testClosureFirstCache()
    {
        $callCount = 0;
        $provider = new Provider(function($index) use (&$callCount) {
            $callCount++;
            switch ($index) {
                case 0: return ['one'];
                case 1: return ['two'];
                case 2: return ['three'];
                default: return [];
            }
        });

        // simulate iteration of single element
        $provider->rewind();
        $provider->valid();
        $current = $provider->current();
        $first = $provider->first();
        $expected = 'one';
        $this->assertEquals($expected, $current);
        $this->assertEquals($expected, $first);
        $this->assertEquals(1, $callCount);
        // ensure callCount is still one if first is called again
        $actual = $provider->first();
        $this->assertEquals($expected, $actual);
        $this->assertEquals(1, $callCount);
    }

    /**
     * @covers Jsvrcek\ICS\Utility\Provider::next
     * @covers Jsvrcek\ICS\Utility\Provider::current
     */
    public function testClosureIterate()
    {
        $provider = new Provider(function($index) {
            switch ($index) {
                case 0: return ['one'];
                case 1: return ['two'];
                case 2: return ['three'];
                default: return [];
            }
        });

        $expected = ['one', 'two', 'three'];
        $actual = [];
        foreach ($provider as $item) {
            $actual[] = $item;
        }
        $this->assertEquals($expected, $actual);
    }

}
