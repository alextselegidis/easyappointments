<?php

namespace Jsvrcek\ICS\Utility;

class Provider implements \Iterator
{
    /**
     * 
     * @var \Closure
     */
    private $provider;
    
    /**
     * 
     * @var array
     */
    public $data = array();
    
    /**
     *
     * @var array
     */
    public $manuallyAddedData = array();
    
    /**
     * 
     * @var integer
     */
    private $key;
    
    /**
     * @param \Closure $provider An optional closure for adding items in batches during iteration. The closure will be
     *     called each time the end of the internal data array is reached during iteration, and the current data
     *     array key value will be passed as an argument. The closure should return an array containing the next 
     *     batch of items.
     */
    public function __construct(\Closure $provider = null)
    {
        $this->provider = $provider;
    }
    
    /**
     * for manually adding items, rather than using a provider closure to add items in batches during iteration
     * Cannot be used in conjunction with a provider closure!
     * 
     * @param mixed $item
     */
    public function add($item)
    {
        $this->manuallyAddedData[] = $item;
    }
    
    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        return current($this->data);
    }
    
    /* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->key;
    }
    
    /* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        array_shift($this->data);
        $this->key++;
    }
    
    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->data = array();
        $this->key = 0;
    }
    
    /** 
     * get next batch from provider if data array is at the end
     * 
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        if (count($this->data) < 1)
        {
            if ($this->provider instanceof \Closure)
            {
                $this->data = $this->provider->__invoke($this->key);
            }
            else
            {
                $this->data = $this->manuallyAddedData;
                $this->manuallyAddedData = array();
            }
        }
        
        return count($this->data) > 0;
    }
}