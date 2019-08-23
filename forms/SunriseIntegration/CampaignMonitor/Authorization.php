<?php

namespace SunriseIntegration\CampaignMonitor;

use SunriseIntegration\CampaignMonitor\AbstractObject;

class Authorization extends AbstractObject{

	const OAUTH = 1;
	const HTTP_BASIC = 2;

	private $access_token;
	private $refresh_token;
	private $expires_in;
	private $api_key;
	private $username;
	private $password;
	private $type = self::OAUTH;

	/**
	 * Authorization constructor.
	 *
	 * @param array $parameters
	 */
	public function  __construct($parameters = []) {

		if ( ! empty( $parameters ) ) {
			foreach ( $parameters as $method => $value ) {
				$method = "set$method";
				$method = str_replace( [ '_', ' ' ], '', $method );

				if ( method_exists( $this, $method ) ) {
					$this->{$method}( $value );
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getAccessToken() {
		return $this->access_token;
	}

	/**
	 * @param mixed $access_token
	 *
	 * @return Authorization
	 */
	public function setAccessToken( $access_token ) {
		$this->access_token = $access_token;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRefreshToken() {
		return $this->refresh_token;
	}

	/**
	 * @param mixed $refresh_token
	 *
	 * @return Authorization
	 */
	public function setRefreshToken( $refresh_token ) {
		$this->refresh_token = $refresh_token;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExpiresIn() {
		return $this->expires_in;
	}

	/**
	 * @param mixed $expires_in
	 *
	 * @return Authorization
	 */
	public function setExpiresIn( $expires_in ) {
		$this->expires_in = $expires_in;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApiKey() {
		return $this->api_key;
	}

	/**
	 * @param mixed $api_key
	 *
	 * @return Authorization
	 */
	public function setApiKey( $api_key ) {
		$this->api_key = $api_key;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param mixed $username
	 *
	 * @return Authorization
	 */
	public function setUsername( $username ) {
		$this->username = $username;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param mixed $password
	 *
	 * @return Authorization
	 */
	public function setPassword( $password ) {
		$this->password = $password;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 *
	 * @return Authorization
	 */
	public function setType( $type ) {
		$this->type = $type;

		return $this;
	}
}
