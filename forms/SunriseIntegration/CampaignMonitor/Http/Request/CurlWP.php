<?php

namespace SunriseIntegration\CampaignMonitor\Http\Request;

use SunriseIntegration\CampaignMonitor\Http\Request;
use SunriseIntegration\CampaignMonitor\IRequest;

/**
 * Sends wp_ wp_safe_remote_post.
 * Note: this requires the cURL extension to be enabled in PHP
 * @see https://developer.wordpress.org/reference/functions/wp_safe_remote_post/
 */
class CurlWP implements IRequest
{

	private $lastRequest;


	/**
	 * @param Request $params
	 *
	 * @return array|mixed|string|void|\WP_Error
	 */
	public function send(Request $request)
	{
		$method = 'POST';
		switch ( $request->getMethod() ) {
			case IRequest::METHOD_POST:
				$method = 'POST';
				break;
			case IRequest::METHOD_GET:
				$method = 'GET';
				break;
			case IRequest::METHOD_PUT :
				$method = 'PUT';
				break;
			case IRequest::METHOD_DELETE :
				$method = 'DELETE';
				break;
		}
		$headers = $request->getHeaders()->toArray();
		$response = wp_safe_remote_request( $request->getUri(), array(
				'method' => $method,
				'headers' => $headers,
				'body' => $request->getBody() )
		);

		$request->getHeaders()->remove( 'Authorization' );

		$this->setLastRequest( ['response' => $response, 'request' => $request ] );

		$response = wp_remote_retrieve_body( $response );

		return  $response;

	}

	public function getLastRequest() {
		return $this->lastRequest;
	}

	private function setLastRequest($request){
		$this->lastRequest = $request;
		return $this;
	}
}
