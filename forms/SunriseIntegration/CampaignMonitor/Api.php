<?php
namespace SunriseIntegration\CampaignMonitor;

use SunriseIntegration\CampaignMonitor\Http\Request;
use forms\core\Log;

class Api
{
	const API_URL = 'https://api.createsend.com/api/v3.1';
	const RETURN_JSON = 1;
	const RETURN_XML = 2;

	protected $returnType = self::RETURN_JSON;

	/**
	 * @var \SunriseIntegration\CampaignMonitor\IRequest
	 */
	private $request;
	/**
	 * @var \SunriseIntegration\CampaignMonitor\Authorization
	 */
	private $authorization = null;

	/**
	 * @return \SunriseIntegration\CampaignMonitor\Authorization
	 */
	public function getAuthorization() {
		return $this->authorization;
	}

	/**
	 * @param \SunriseIntegration\CampaignMonitor\Authorization $authorization
	 *
	 * @return Api
	 */
	public function setAuthorization( $authorization ) {
		$this->authorization = $authorization;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReturnType() {
		return $this->returnType;
	}

	/**
	 * @param mixed $returnType
	 *
	 * @return Api
	 */
	public function setReturnType( $returnType ) {
		$this->returnType = $returnType;

		return $this;
	}

	/**
	 * Api constructor.
	 *
	 * @param \SunriseIntegration\CampaignMonitor\Authorization $auth
	 * @param \SunriseIntegration\CampaignMonitor\IRequest|null $requestMethod
	 * @throws \RuntimeException
	 */
	public function __construct( \SunriseIntegration\CampaignMonitor\Authorization $auth,\SunriseIntegration\CampaignMonitor\IRequest $requestMethod = null)
	{
		if ($auth === null) {
			throw new \RuntimeException('Authorization is required for the api to work!');
		}

		$this->setAuthorization( $auth );

		if ($requestMethod !== null) {
			$this->setRequest( $requestMethod );
		} else {
			$this->setRequest( new Http\Request\Curl() );
		}
	}

	/**
	 * all call to the  specified endpoint
	 * @param $endpoint
	 * @param null $query
	 *
	 * @return mixed
	 */
	protected function getData($endpoint, $query = null) {

		$request = new Request( $this->getAuthorization(), [] );
		$headers = new \SunriseIntegration\CampaignMonitor\Http\Headers();
		$headers->add( 'Accept', 'application/json' );
		$headers->add( 'Content-Type', 'application/json' );
		$headers->add( 'Cache-Control', 'no-cache' );
		$headers->add( 'Authorization', 'Bearer ' . $this->getAuthorization()->getAccessToken() );
		$headers->add('X-Forwarded-For', $this->getRemoteUserIp() );
		$request->setHeaders( $headers );
		$request->setMethod( IRequest::METHOD_GET );

		$extension = '';

		switch ( $this->getReturnType() ) {
			case self::RETURN_JSON:
				$extension = 'json';
				break;
			case self::RETURN_XML;
				$extension = 'xml';
				break;
		}

		$endpoint = "/$endpoint.$extension";

		if ( $query !== null ) {
			$endpoint .= '?' . http_build_query( $query );
		}

		$request->setUri( self::API_URL . $endpoint );

		$result = $this->getRequest()->send($request);
		$this->logData( $this->getRequest()->getLastRequest() );
		return $result;
	}


	public function getRemoteUserIp() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	/**
	 * Routes all call to the  specified endpoint
	 * @param $endpoint
	 * @param $dataToPost
	 * @param string $httpMethod
	 *
	 * @return mixed
	 */
	protected function postData($endpoint, $dataToPost, $httpMethod = IRequest::METHOD_POST) {

		$request = new Request( $this->getAuthorization(), [] );

		$headers = new \SunriseIntegration\CampaignMonitor\Http\Headers();
		$headers->add( 'Accept', 'application/json' );
		$headers->add( 'Content-Type', 'application/json' );
		$headers->add( 'Cache-Control', 'no-cache' );
		$headers->add( 'Authorization', 'Bearer ' . $this->getAuthorization()->getAccessToken() );
		$headers->add('X-Forwarded-For', $this->getRemoteUserIp() );

		$request->setHeaders( $headers );
		$request->setMethod( $httpMethod );
		$request->setBody( json_encode( $dataToPost ) );

		$extension = '';

		switch ( $this->getReturnType() ) {
			case self::RETURN_JSON:
				$extension = 'json';
				break;
			case self::RETURN_XML;
				$extension = 'xml';
				break;
		}
		$request->setUri( self::API_URL . "/$endpoint.$extension" );
		$result = $this->getRequest()->send($request);
		$this->logData( $this->getRequest()->getLastRequest() );
		return $result;
	}

	private function logData($data){
		Log::write( base64_encode(print_r($data, true)) );
	}

	public function refreshToken($token = '') {

		if ( $token === '' ) {
			$token = $this->getAuthorization()->getRefreshToken();
		}

		$dataToPost = [
			'grant_type' => 'refresh_token',
			'refresh_token' => $token
		];

		$request = new Request( $this->getAuthorization(), [] );

		$headers = new \SunriseIntegration\CampaignMonitor\Http\Headers();
		$headers->add( 'contentType', 'application/x-www-form-urlencoded' );

		$request->setHeaders( $headers );

		$request->setMethod( IRequest::METHOD_POST );
		$query = http_build_query( $dataToPost );
		$request->setBody(  $query );


		$url = 'https://api.createsend.com/oauth/token';

		$request->setUri( $url  );
		return $this->getRequest()->send($request);
	}

	public function getClientDetails( $id ) {

		return $this->getData( "clients/$id" );
	}

	/**
	 * @return IRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @param IRequest $request
	 *
	 * @return Api
	 */
	public function setRequest( $request ) {
		$this->request = $request;

		return $this;
	}

	public function getClients($asJson = false) {

		return $this->getData( 'clients' );
	}

	public function getTimezones($asJson = false) {
		return $this->getData( 'timezones' );
	}

	public function createClient( $clientObject ) {
		return $this->postData( 'clients', $clientObject );
	}

	public function getSubscriber( $listId, $email, $asJson = false ) {
		return $this->getData( "subscribers/$listId", ['email'=> $email] );
	}

	public function getStats( $listId ) {
		return $this->getData( "lists/{$listId}/stats" );
	}

	public function getListDetails($id) {
		return $this->getData( "lists/$id" );
	}

	public function sendEmail( $to, $listName, $message = array(), $type = 'json' ) {

		$endpoint                      = 'https://integrationstore-5d74b11ccbdb8fa8.microservice.createsend.com/campaign-monitor-for-woo-commerce/email/data-synced';
		$dataToPost                    = array();
		$dataToPost['ToEmail']         = $to;
		$dataToPost['ListName']        = $listName;
		$dataToPost['SubscriberCount'] = $message['subscribers_count'];

		switch ( $type ) {
			case 'urlencode':
				$dataToPost = http_build_query( $dataToPost );
				break;
			case 'json':
				$dataToPost = json_encode( $dataToPost );
				break;
		}

		$request = new Request( $this->getAuthorization(), [] );

		$headers = new \SunriseIntegration\CampaignMonitor\Http\Headers();
		$headers->add( 'Accept', 'application/json' );
		$headers->add( 'Content-Type', 'application/json' );
		$headers->add( 'Cache-Control', 'no-cache' );
		$headers->add( 'Authorization', 'Bearer ' . $this->getAuthorization()->getAccessToken() );
		$headers->add( 'X-Forwarded-For', $this->getRemoteUserIp() );

		$request->setHeaders( $headers );
		$request->setMethod( IRequest::METHOD_POST );
		$request->setBody( $dataToPost );

		$this->getAuthorization()->setType( Authorization::OAUTH );
		$request->setUri( $endpoint );

		return $this->getRequest()->send( $request );

	}

	public function importSubscribers($listId, $subscribers) {

		$subscribersData = array(
			'Resubscribe' => false,
			'QueueSubscriptionBasedAutoResponders' => true,
			'Subscribers' => $subscribers,
			'RestartSubscriptionBasedAutoresponders' => false
		);

		return $this->postData( "subscribers/{$listId}/import", $subscribersData );
	}

	public function addSubscriber( $lisId, $subscriberData ) {
		return $this->postData( "subscribers/{$lisId}", $subscriberData );
	}

	public function createSegment( $listId, $segment ) {
		return $this->postData( "segments/{$listId}", $segment );
	}

	/**
	 * @param string $listId The ID of the list to which the custom field belongs.
	 * @param string $fieldKey The key of the custom field you want to update. This key is provided for each field returned when getting list custom fields
	 * @param array $field = [ "FieldName" => "Newsletter Format Renamed",	"VisibleInPreferenceCenter" => false ]
	 *
	 * @return mixed
	 */
	public function updateCustomField( $listId, $fieldKey, $field = array() ) {
		$cleanKey = str_replace( array( '[', ']' ), '', $fieldKey );
		$fieldKey = \rawurlencode( "[{$cleanKey}]");

		return $this->postData( "lists/{$listId}/customfields/$fieldKey", $field, IRequest::METHOD_PUT );
	}

	/**
	 * @param string $listId The ID of the list you want to create the custom field on.
	 * @param array $field [ "FieldName" => "Newsletter Format", "DataType" => "MultiSelectOne",  "Options" => [ "HTML", "Text" ],"VisibleInPreferenceCenter" => true]
	 *
	 * @return mixed
	 */
	public function createCustomField( $listId, $field ) {
		return $this->postData( "lists/{$listId}/customfields", $field );
	}

	/**
	 * @param $clientId
	 * @param bool $asJson
	 *
	 * @return mixed
	 */
	public function getLists( $clientId, $asJson = false ) {
		return $this->getData( "clients/$clientId/lists" );
	}

	/**
	 * @param  string $clientId
	 * @param array $listData
	 *
	 * @return mixed
	 */
	public function createList( $clientId, $listData ) {
		return $this->postData( "lists/{$clientId}", $listData );
	}

	public function getCustomFields( $listId, $asJson = false ) {
		return $this->getData( "lists/{$listId}/customfields" );
	}

	public function getSegments($listId, $asJson = false) {
		return $this->getData( "lists/{$listId}/segments" );
	}

	public function getSegmentDetails( $segmentId, $asJson = false ) {
		return $this->getData( "segments/{$segmentId}" );
	}

}
