<?php

namespace Jsvrcek\ICS\Model\Description;

class Geo
{
    /**
     * @var float
     */
    private $latitude;

    /**
     *
     * @var float
     */
    private $longitude;

    /**
     * @return number
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return \Jsvrcek\ICS\Model\Description\Geo
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return number
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return \Jsvrcek\ICS\Model\Description\Geo
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }
}
