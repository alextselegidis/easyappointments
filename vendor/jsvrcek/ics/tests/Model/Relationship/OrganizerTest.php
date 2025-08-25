<?php

namespace Jsvrcek\ICS\Tests\Model\Relationship;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use PHPUnit\Framework\TestCase;

class OrganizerTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\Model\Organizer::__toString()
     */
    public function testToString()
    {
        $object = new Organizer(new Formatter());

        $object
            ->setValue('leonardo@example.it')
            ->setName('Leonardo Da Vinci')
            ->setLanguage('it')
            ->setDirectory('http://en.wikipedia.org/wiki/Leonardo_da_Vinci')
            ->setSentBy('piero@example.com');
        
        $expected = 'ORGANIZER;SENT-BY="piero@example.com";CN=Leonardo Da Vinci;DIR="http://en.wikipedia.org/wiki/Leonardo_da_Vinci";LANGUAGE=it:mailto:leonardo@example.it';
        
        $this->assertEquals($expected, $object->__toString());
    }
}
