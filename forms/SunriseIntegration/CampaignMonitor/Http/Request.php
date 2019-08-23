<?php

namespace SunriseIntegration\CampaignMonitor\Http;

use SunriseIntegration\CampaignMonitor\Authorization;
use SunriseIntegration\CampaignMonitor\AbstractObject;
use SunriseIntegration\CampaignMonitor\IRequest;

/**
 * Stores and formats the parameters for the request 
 */
class Request extends AbstractObject
{
	private $authorization;
	private $dataToProcess;
	private $uri;
	/**
	 * @var Headers
	 */
	private $headers;
	private $method = IRequest::METHOD_POST;
	private $body;


	/**
	 * RequestParameters constructor.
	 *
	 * @param Authorization $auth
	 * @param $data
	 */
    public function __construct(Authorization $auth, $data)
    {
		$this->authorization = $auth;
	    $this->dataToProcess = $data;
    }

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param mixed $body
	 *
	 * @return Request
	 */
	public function setBody( $body ) {
		$this->body = $body;

		return $this;
	}



	/**
	 * @return Authorization
	 */
	public function getAuthorization() {
		return $this->authorization;
	}

	/**
	 * @param Authorization $authorization
	 *
	 * @return Request
	 */
	public function setAuthorization( $authorization ) {
		$this->authorization = $authorization;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDataToProcess() {
		return $this->dataToProcess;
	}

	/**
	 * @param mixed $dataToProcess
	 *
	 * @return Request
	 */
	public function setDataToProcess( $dataToProcess ) {
		$this->dataToProcess = $dataToProcess;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * @param mixed $uri
	 *
	 * @return Request
	 */
	public function setUri( $uri ) {
		$this->uri = $uri;

		return $this;
	}

	/**
	 * @return Headers
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param Headers $headers
	 *
	 * @return Request
	 */
	public function setHeaders( $headers ) {
		$this->headers = $headers;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param mixed $method
	 *
	 * @return Request
	 */
	public function setMethod( $method ) {
		$this->method = $method;

		return $this;
	}




    /**
     * Query string representation for HTTP request.
     *
     * @return string Query string formatted parameters.
     */
    public function toQueryString()
    {
        return http_build_query($this->toArray(), '', '&');
    }
}
