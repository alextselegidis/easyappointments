<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.3
 * ---------------------------------------------------------------------------- */

/**
 * Class Ics_calendar
 *
 * This class replaces the Jsvrcek\ICS\Utility\Provider so that it becomes a PHP 8.1 compatible Iterator class.
 *
 * Since the method signatures changed in PHP 8.1, the ReturnTypeWillChange attribute allows us to keep compatibility
 * between different PHP versions.
 */
class Ics_provider implements \Iterator {
    /**
     * @var \Closure
     */
    private $provider;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public $manuallyAddedData = [];

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
    public function __construct(\Closure $provider = NULL)
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
    #[ReturnTypeWillChange]
    public function add($item)
    {
        $this->manuallyAddedData[] = $item;
    }

    /**
     * @return false|mixed
     * @see Iterator::current()
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    /**
     * @return float|int|null
     * @see Iterator::key()
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->key;
    }

    /**
     * @return void
     * @see Iterator::next()
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        array_shift($this->data);
        $this->key++;
    }

    /**
     * @return void
     * @see Iterator::rewind()
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->data = [];
        $this->key = 0;
    }

    /**
     * get next batch from provider if data array is at the end
     *
     * @return bool
     * @see Iterator::valid()
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        if (count($this->data) < 1)
        {
            if ($this->provider instanceof \Closure)
            {
                $this->data = $this->provider->__invoke($this->key);
                if (isset($this->data[0]))
                {
                    $this->first = $this->data[0];
                }
            }
            else
            {
                $this->data = $this->manuallyAddedData;
                $this->manuallyAddedData = [];
            }
        }

        return count($this->data) > 0;
    }

    /**
     * Returns first event
     *
     * @return false|mixed
     */
    #[ReturnTypeWillChange]
    public function first()
    {
        if (isset($this->first))
        {
            return $this->first;
        }

        if ($this->provider instanceof \Closure)
        {
            if ($this->valid())
            {
                return $this->first;
            }
            else
            {
                return FALSE;
            }
        }

        if ( ! isset($this->manuallyAddedData[0]))
        {
            return FALSE;
        }

        return $this->manuallyAddedData[0];
    }
}
