<?php

namespace SunriseIntegration\CampaignMonitor;



/**
 * Class AbstractObject
 * @SuppressWarnings(PHPMD)
 * @package SunriseIntegration\CampaignMonitor
 */
abstract class AbstractObject implements \JsonSerializable, \ArrayAccess {

	/**
	 * Object attributes
	 *
	 * @var array
	 */
	protected $_data = [];


	/**
	 * @return array
	 */
	public function toArray() {
		$properties = get_object_vars( $this );

		return $this->convertToArray( $properties );
	}


	/**
	 * @param array $properties
	 *
	 * @return array
	 */
	protected function convertToArray( array $properties ) {
		foreach ( $properties as $key => $value ) {

			if ( is_array( $value ) ) {
				$properties[ $key ] = $this->convertToArray( $value );
			} else {
				if ( is_object( $value ) && method_exists( $value, 'toArray' ) ) {
					$properties[ $key ] = $value->toArray();
				}
			}
		}

		return $properties;
	}

	/**
	 * Convert object data to JSON
	 *
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this);
	}

	/**
	 * Implementation of \JsonSerializable::jsonSerialize()
	 *
	 * Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
	 * @return mixed
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Implementation of \ArrayAccess::offsetSet()
	 *
	 * @param string $offset
	 * @param mixed $value
	 * @return void
	 * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Implementation of \ArrayAccess::offsetExists()
	 *
	 * @param string $offset
	 * @return bool
	 * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
	 */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]) || array_key_exists($offset, $this->_data);
	}

	/**
	 * Implementation of \ArrayAccess::offsetUnset()
	 *
	 * @param string $offset
	 * @return void
	 * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	/**
	 * Implementation of \ArrayAccess::offsetGet()
	 *
	 * @param string $offset
	 * @return mixed
	 * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
	 */
	public function offsetGet($offset)
	{
		if (isset($this->_data[$offset])) {
			return $this->_data[$offset];
		}
		return null;
	}


	public function getData( $key = '' ) {

		if ( $key !== '' && array_key_exists( $key, $this->_data ) ) {
			return $this->_data[ $key ];
		}

		return $this->_data;
	}

}