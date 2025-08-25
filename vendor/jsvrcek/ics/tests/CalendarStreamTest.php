<?php

namespace Jsvrcek\ICS\Tests;

use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Constants;
use PHPUnit\Framework\TestCase;

class CalendarStreamTest extends TestCase
{
    public function testGetStream()
    {
        $s = new CalendarStream();
        $this->assertEquals('', $s->getStream());
    }
    
    public function testAddItemToStream()
    {
        //simple test
        $s = new CalendarStream();
        $item = 'TEST';
        $expected = 'TEST'.Constants::CRLF;
        $s->addItem($item);
        $this->assertEquals($expected, $s->getStream());
        
        //long string test
        $s = new CalendarStream();
        $item = ' aaaaaaa10 aaaaaaa20 aaaaaaa30 aaaaaaa40 aaaaaaa50 aaaaaaa60 aaaaaaa70 aaaaaaa80 aaaaaaa90';
        $expected = ' aaaaaaa10 aaaaaaa20 aaaaaaa30 aaaaaaa40 aaaaaaa50 aaaaaaa60 aaaaaaa70'.Constants::CRLF.' '.' aaaaaaa80 aaaaaaa90'.Constants::CRLF;
        $s->addItem($item);
        $this->assertEquals($expected, $s->getStream());
        
        $s = new CalendarStream();
        $item = ' ëëëë1 ëëëë2 ëëëë3 ëëëë4 ëëëë5 ëëëë6 ëëëëëëëëë8 ëëëë9';
        $expected = ' ëëëë1 ëëëë2 ëëëë3 ëëëë4 ëëëë5 ëëëë6 ëëëë'.Constants::CRLF.' '.'ëëëëë8 ëëëë9'.Constants::CRLF;
        $s->addItem($item);
        $this->assertEquals($expected, $s->getStream());
    }
    
    public function testReset()
    {
        $s = new CalendarStream();
        $item = 'TEST';
        $s->addItem($item);
        $s->reset();
        $this->assertEquals('', $s->getStream());
    }
    
    public function test__toString()
    {
        $s = new CalendarStream();
        $item = 'TEST';
        $s->addItem($item);
        $expected = 'TEST'.Constants::CRLF;
        $this->assertEquals($expected, $s->__toString());
    }
}
