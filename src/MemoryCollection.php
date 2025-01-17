<?php

namespace Live\Collection;

use DateTime;

/**
 * Memory collection
 *
 * @package Live\Collection
 */
class MemoryCollection implements CollectionInterface
{
    /**
     * Collection data
     *
     * @var array
     */
    protected $data;
	
	/**
     * Collection time
	 * stores the expiry date of the index
     *
     * @var array
     */
    protected $expirationTime;

    /**
     * Constructor
     * initialize class with empty collection
     */
    public function __construct()
    {
        $this->data = [];
        $this->expirationTime = [];
    }

    /**
     * Return a value by index
     * @param string $index
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }

        return $this->data[$index];
    }

    /**
     * Add a value to the collection
     * @param string $index
     * @param mixed $value
     * return void
     */
    public function set(string $index, $value, date $expirationTime = null)
    {
		if(empty($expirationTime)) {
			$date = new DateTime(date('Y-m-d H:m'));
			$date->modify('+1 day');
			$expirationTime = $date->format('Y-m-d H:m');
		}
        $this->data[$index] = $value;
		$this->expirationTime[$index] = $expirationTime;
    }

    /**
     * Checks whether the collection has th given index
     * checks if the expiration date is greater than the current one
     * @param string $index
     * @return bool
     */
    public function has(string $index)
    {
        return array_key_exists($index, $this->data) 
			&& (!empty($this->expirationTime[$index]) && $this->expirationTime[$index] > date('Y-m-d H:m'));
    }

    /**
     * return the count of itens in the collection
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * clean the collection
     * return void
     */
    public function clean()
    {
        $this->data = [];
    }
}
