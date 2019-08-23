<?php

namespace SunriseIntegration\CampaignMonitor\Http\Request;

use SunriseIntegration\CampaignMonitor\Http\Request;
use SunriseIntegration\CampaignMonitor\IRequest;
use SunriseIntegration\CampaignMonitor\Authorization;

/**
 * Convenience wrapper around the cURL functions to allow mocking.
 */
class Curl implements IRequest
{
	protected $lastRequest;

	/**
	 * Curl connection
	 * @var Curl
	 */
	private $curl;

	public function __construct(Curl $curl = null)
	{
		if ($curl !== null) {
			$this->curl = $curl;
		}
	}

	protected function setLastRequest($request) {
		$this->lastRequest = $request;

		return $this;
	}


	public function send(Request $request )
	{

		$handle = $this->init();

		$options = array(
			CURLOPT_URL => $request->getUri()
,			CURLOPT_TIMEOUT    => 20,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLINFO_HEADER_OUT    => false,
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_VERBOSE        => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLINFO_HEADER_OUT    => true,
			CURLOPT_SSL_VERIFYHOST =>2,

		);


		$options[ CURLOPT_HTTPHEADER ] = $request->getHeaders()->toStringArray();


		switch ( $request->getMethod() ) {
			case IRequest::METHOD_POST:
				$options[ CURLOPT_POST ] = true;
				break;
			case IRequest::METHOD_GET:
				$options[ CURLOPT_POST ] = 0;
				break;
			case IRequest::METHOD_PUT :
				$options[ CURLOPT_CUSTOMREQUEST ] = $request->getMethod();
				break;
			case IRequest::METHOD_DELETE :
				$options[ CURLOPT_CUSTOMREQUEST ] = $request->getMethod();
				break;
		}

		if (is_array($request->getBody()) && !empty($request->getBody())) {
			$options[ CURLOPT_POSTFIELDS ] = json_encode( $request->getBody() );
		} else if (!empty($request->getBody())) {
			$options[ CURLOPT_POSTFIELDS ] = $request->getBody();
		}

		switch ( $request->getAuthorization()->getType() ) {
			case Authorization::OAUTH :
				$options[ CURLOPT_HTTPAUTH ] = CURLAUTH_BASIC;
				break;
			case Authorization::HTTP_BASIC :
				$options[ CURLOPT_HTTPAUTH ] = CURLAUTH_BASIC;
				$options[ CURLOPT_USERPWD ] = $request->getAuthorization()->getApiKey() . ':' . $request->getAuthorization()->getPassword();
				break;
		}

		$this->setoptArray($handle, $options);

		$response = $this->exec($handle);
		$this->setLastRequest( curl_getinfo( $handle ) );
		$this->close($handle);

		return $response;
	}

    /**
     * @see http://php.net/curl_init
     * @param string $url
     * @return resource cURL handle
     */
    public function init($url = null)
    {
        return curl_init($url);
    }

    /**
     * @see http://php.net/curl_setopt_array
     * @param resource $ch
     * @param array $options
     * @return bool
     */
    public function setoptArray($ch, array $options)
    {
        return curl_setopt_array($ch, $options);
    }

    /**
     * @see http://php.net/curl_exec
     * @param resource $ch
     * @return mixed
     */
    public function exec($ch)
    {
        return curl_exec($ch);
    }

    /**
     * @see http://php.net/curl_close
     * @param resource $ch
     */
    public function close($ch)
    {
        curl_close($ch);
    }

	public function getLastRequest() {
		return $this->lastRequest;
	}
}
