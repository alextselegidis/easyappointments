<?php

namespace Jsvrcek\ICS\Model\Description;

class Location
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $language;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Jsvrcek\ICS\Model\Description\Location
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri uri to vCard or other uri
     * @return \Jsvrcek\ICS\Model\Description\Location
     */
    public function setUri($uri)
    {
        $this->uri = ';ALTREP="' . $uri . '"';
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
     * @return \Jsvrcek\ICS\Model\Description\Location
     */
    public function setLanguage($language)
    {
        $this->language = ';LANGUAGE='.$language;
        return $this;
    }
}
