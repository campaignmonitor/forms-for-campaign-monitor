<?php

namespace forms\core;


abstract class Collection 
{
	
	private $items = array();
	
	public function addItem($obj, $key = null) {
		
		if ($key == null) {
			$this->items[] = $obj;
		}
		else {
			if (isset($this->items[$key])) {
				throw new KeyHasUseException("Key $key already in use.");
			}
			else {
				$this->items[$key] = $obj;
			}
		}
	}
	
	
	public function deleteItem($key) {
		if (isset($this->items[$key])) {
			unset($this->items[$key]);
		}
		else {
			throw new KeyInvalidException("Invalid key $key.");
		}
		
	}
	
	
	public function getItem($key) {
		if (isset($this->items[$key])) {
			return $this->items[$key];
		}
		else {
			throw new KeyInvalidException("Invalid key $key.");
		}
	}

	public function keys() {
    	return array_keys($this->items);
	}

	public function length() {
    	return count($this->items);
	}

	public function keyExists($key) {
    	return isset($this->items[$key]);
	}
}
	