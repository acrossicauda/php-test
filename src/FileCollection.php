<?php

namespace Live\Collection;

use DateTime;

/**
 * Memory collection
 *
 * @package Live\Collection
 */
class FileCollection extends MemoryCollection implements CollectionInterface
{
    /**
     * Collection data
     *
     * @var array
     */
    protected $data;
	
    /**
     * File name, do not use the extension
     *
     * @var string
     */
    protected $fileName;
	
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
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->data = [];
		$this->expirationTime = [];
        $this->fileName = __DIR__ . '/' . $file . '.txt';
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
        $this->data = json_decode(file_get_contents($this->fileName), true);
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
		
        $fp = fopen($this->fileName, 'w');
        fwrite($fp, json_encode($this->data));
        fclose($fp);
    }

    /**
     * Checks whether the collection has th given index
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
     * clean the collection and remove file
     * return void
     */
    public function clean()
    {
        $this->data = [];
        unlink($this->fileName);
    }
}
