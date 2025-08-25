<?php

namespace Jsvrcek\ICS\Model\Relationship;

use Jsvrcek\ICS\Utility\Formatter;

class Organizer
{
    /**
     * @var Formatter
     */
    private $formatter;
    
    /**
     * RFC 5545 cal-address
     * @var string
     */
    private $value;

    /**
     * RFC 5545 cnparam
     * @var string
     */
    private $name;

    /**
     * RFC 5545 dirparam
     * @var string
     */
    private $directory;

    /**
     * RFC 5545 sentbyparam
     * @var string
     */
    private $sentBy;

    /**
     * RFC 5545 languageparam
     * @var string
     */
    private $language;
    
    /**
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }
    
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * RFC 5545 cal-address http://tools.ietf.org/html/rfc5545#section-3.3.3
     * @param string $uri
     * @return \Jsvrcek\ICS\Model\Relationship\Organizer
     */
    public function setValue($uri)
    {
        $this->value = $this->formatter->getFormattedUri($uri);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Jsvrcek\ICS\Model\Relationship\Organizer
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * RFC 5545 dirparam http://tools.ietf.org/html/rfc5545#section-3.2.6
     * @param string $directory uri directory entry associated with the calendar user
     * @return \Jsvrcek\ICS\Model\Relationship\Organizer
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getSentBy()
    {
        return $this->sentBy;
    }

    /**
     * RFC 5545 sentbyparam http://tools.ietf.org/html/rfc5545#section-3.2.18
     * @param string $sentBy email address
     * @return \Jsvrcek\ICS\Model\Relationship\Organizer
     */
    public function setSentBy($sentBy)
    {
        $this->sentBy = $sentBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language RFC 1766 language identifier
     * @return \Jsvrcek\ICS\Model\Relationship\Organizer
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }
    
    public function __toString()
    {
        $string = 'ORGANIZER';
        
        if ($this->sentBy) {
            $string .= ';SENT-BY="'.$this->sentBy.'"';
        }
        
        if ($this->name) {
            $string .= ';CN='.$this->name;
        }
        
        if ($this->directory) {
            $string .= ';DIR="'.$this->directory.'"';
        }
        
        if ($this->language) {
            $string .= ';LANGUAGE='.$this->language;
        }
        
        $string .= ':'.$this->value;
        
        return $string;
    }
}
