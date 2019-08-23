<?php

namespace SunriseIntegration\CampaignMonitor\Http;

use SunriseIntegration\CampaignMonitor\AbstractObject;

/**
 * Stores and formats the parameters for the request
 */
class Headers extends AbstractObject
{
	protected $_headers = array();

	public function add($key, $value){
		$this->_headers[$key] = $value;
	}

	public function get( $key = '' ) {

		if ( $key === '' ) {
			return $this->_headers;
		}

		if ( array_key_exists( $key, $this->_headers ) ) {
			return $this->_headers[ $key ];
		}

		return null;
	}

	public function remove( $key ) {
		if ( array_key_exists( $key, $this->_headers ) ) {
			unset( $this->_headers[ $key ]);
		}
	}


	public function toArray() {
		return $this->_headers;
	}

	public function toStringArray() {

		$headers = array();
		if ( ! empty( $this->_headers ) ) {

			foreach ( $this->_headers as $key => $value ) {
				$headers[] = "$key:$value";
			}
		}

		return $headers;
	}


}
