<?php


namespace Jsvrcek\ICS\Model\Description;


class Conference
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string|string[]
     */
    private $feature;

    /**
     * @var string
     */
    private $language;

    /**
     * Conference constructor. Options can contain any of the following values:
     *  - label
     *  - feature
     *  - language
     * @link https://tools.ietf.org/html/rfc7986#section-5.11
     * @param $uri
     * @param array $options
     */
    public function __construct($uri, array $options=[])
    {
        $this->uri = $uri;
        $this->label = isset($options['label']) ? $options['label'] : '';
        $this->feature = isset($options['feature']) ? $options['feature'] : '';
        $this->language = isset($options['language']) ? $options['language'] : '';
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getFeature()
    {
        if (is_array($this->feature)) {
            return implode(',', $this->feature);
        }
        return $this->feature;
    }

    /**
     * @param string|string[] $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
