<?php

namespace Jsvrcek\ICS\Utility;

class Provider implements \Iterator
{
    /**
     * @var \Closure
     */
    private $provider;

    /**
     * @var array
     */
    public $data = array();

    /**
     * @var array
     */
    public $manuallyAddedData = array();

    /**
     * @var integer
     */
    private $key;

    /**
     * @var mixed
     */
    private $first;

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
     * @return void
     */
    public function add($item)
    {
        $this->manuallyAddedData[] = $item;
    }

    /**
     * @see Iterator::current()
     * @return false|mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    /**
     * @see Iterator::key()
     * @return float|int|null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->key;
    }

    /**
     * @see Iterator::next()
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        array_shift($this->data);
        $this->key++;
    }

    /**
     * @see Iterator::rewind()
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->data = array();
        $this->key = 0;
    }

    /**
     * get next batch from provider if data array is at the end
     *
     * @see Iterator::valid()
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        if (count($this->data) < 1) {
            if ($this->provider instanceof \Closure) {
                $this->data = $this->provider->__invoke($this->key);
                if (isset($this->data[0])) {
                    $this->first = $this->data[0];
                }
            } else {
                $this->data = $this->manuallyAddedData;
                $this->manuallyAddedData = array();
            }
        }

        return count($this->data) > 0;
    }

    /**
     * Returns first event
     *
     * @return false|mixed
     */
    public function first()
    {
        if (isset($this->first)) {
            return $this->first;
        }

        if ($this->provider instanceof \Closure) {
            if ($this->valid()) {
                return $this->first;
            } else {
                return false;
            }
        }

        if (!isset($this->manuallyAddedData[0])) {
            return false;
        }

        return $this->manuallyAddedData[0];
    }
}
