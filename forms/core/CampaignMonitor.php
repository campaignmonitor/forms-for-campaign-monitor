<?php
namespace forms\core;

use SunriseIntegration\CampaignMonitor\Api;
use SunriseIntegration\CampaignMonitor\Authorization;

class CampaignMonitor
{
    protected $auth = array();
    protected static $errors = array();

    private $api;

    public function __construct($accessToken, $refreshToken)
    {
        $this->auth = array('access_token' => $accessToken,
            'refresh_token' => $refreshToken);

        $auth = new Authorization();
        $auth->setAccessToken($accessToken);
        $auth->setRefreshToken($refreshToken);
        $auth->setType(Authorization::OAUTH);

        $this->api = new Api($auth, new \SunriseIntegration\CampaignMonitor\Http\Request\CurlWP());
    }

    public function get_last_error(){
        if (!empty(self::$errors)){
            return end(self::$errors);
        }
        return '';
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function refresh_token($auth)
    {
        return json_decode($this->api->refreshToken($auth['refresh_token']));
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_clients($credentials)
    {
	    if (empty($credentials)) {
            return (object) ['error' => true];              
        }

        $this->update_tokens($credentials['access_token'], $credentials['refresh_token']);

	    $clients = $this->api->getClients();

	    return $clients !== null ? json_decode($clients) : $clients;

    }

    public function update_tokens($accessToken, $refreshToken) {
        $auth = new Authorization();
        $auth->setAccessToken($accessToken);
        $auth->setRefreshToken($refreshToken);
        $auth->setType(Authorization::OAUTH);

        $this->api->setAuthorization($auth);
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function instantiate_url($integrationKey, $uri, $scope, $state = false)
    {

        $params['integration_key'] = $integrationKey;
        $params['domain_uri'] = $uri;
        $params['scope'] = $scope;
        if ($state) {
            $params['state'] = $state;
        }

        $url = http_build_query($params);

        return 'https://api.createsend.com/oauth' . '?' . $url ;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function authorize_url( $client_id, $redirect_uri, $scope, $state = NULL) {
	    $params = [];

	    $params['client_id']    = $client_id;
	    $params['redirect_uri'] = $redirect_uri;
	    $params['scope']        = $scope;
	    $params['x-forwarded-for'] =  $this->api !== null  ? $this->api->getRemoteUserIp() : 0;
	    if ( $state ) {
		    $params['state'] = $state;
	    }

	    $queryString = http_build_query( $params );

	    return 'https://api.createsend.com/oauth' . '?' . $queryString;
    }



    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of timezones
     */
    public function get_timezones($auth = array())
    {
	    $timezones = json_encode( $this->api->getTimezones() );

	    return $timezones;
    }

    /**
     * @param array $clientObject array(
                                    'CompanyName' => 'Clients company name',
                                    'Country' => 'Clients country',
                                    'Timezone' => 'Clients timezone'
                                    )
     * @param array $auth override the class authentication credentials
     * @return mixed| client id
     */
    public function create_client($clientObject, $auth = array())
    {

	    $response = $this->api->createClient( $clientObject );

	    return $response !== null ? json_decode( $response ) : $response;
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_subscriber($listId, $email, $auth = array())
    {
	    $subscriber = $this->api->getSubscriber( $listId, $email );

        return $subscriber !== null ? json_decode($subscriber) : $subscriber;

    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_stats($listId, $auth = array())
    {
	    return json_decode( $this->api->getStats( $listId ) );
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_list_details($listId, $auth = array())
    {
	    $listDetails = json_decode( $this->api->getListDetails( $listId ) );

	    return $listDetails;
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function send_email($to, $listName, $message = array(), $auth = array()) {

	    $this->api->sendEmail( $to, $listName, $message, 'json' );
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function import_subscribers($listId, $data, $auth = array())
    {

	    return $this->api->importSubscribers( $listId, $data );
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed
     */
    public function add_subscriber($listId, $data, $auth = array())
    {

	    $result = $this->api->addSubscriber( $listId, $data );

	    return json_decode( $result );
    }

    /**
     * @param string $listId
     * @param string $name
     * @param array $segmentsRules to for this segment of the form array(array('RuleType' => '', 'Clause' => ''))
     * @param array $auth
     * @return null
     */
    public function create_segment($listId, $segment, $auth = array())
    {

	   return  $this->api->createSegment( $listId, $segment );
    }

    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function update_custom_field($listId,$fieldKey, $fieldName, $visibleInPreferenceCenter = true, $auth = array())
    {

        $field = array(
            'FieldName' => $fieldName,
            'VisibleInPreferenceCenter'=> $visibleInPreferenceCenter
        );

        $createdField = $this->api->updateCustomField($listId, $fieldKey, $field);

	    return $createdField !== null ? json_decode( $createdField ) : $createdField;
    }
    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function create_custom_field($listId,$fieldName, $dataType, $options = array(), $visibleInPreferenceCenter = true, $auth = array())
    {

	    $fieldData = array(
		    'FieldName' => $fieldName,
		    'DataType' => $dataType,
		    'Options' => $options,
		    'VisibleInPreferenceCenter'=> $visibleInPreferenceCenter
	    );

	    return $this->api->createCustomField( $listId, $fieldData );

    }

    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     * @deprecated
     */
    public function get_client_list($clientId, $auth = array())
    {
        return json_decode($this->api->getLists($clientId)) ;
    }

    /**
     * @param $clientId for which to whom create the list
     * @param array $auth override the class authentication credentials
     * @return mixed|null list id
     */
    public function create_list($clientId, $listTitle, $confirmedOptIn = false, $unsubscribePage = '', $confirmationPage = '', $auth = array())
    {
        $listOptions = array(
                'Title' => $listTitle,
                'UnsubscribePage' => $unsubscribePage,
                'ConfirmedOptIn' => $confirmedOptIn,
                'ConfirmationSuccessPage' => $confirmationPage,
                'UnsubscribeSetting' => 'AllClientLists'
            );

        return json_decode($this->api->createList($clientId,$listOptions));

    }

    /**
     *
     * @param $listId the id for which you want the custom fields from
     * @param array $auth override the class authentication credentials
     * @return mixed|null custom field list
     */
    public function get_custom_fields($listId, $auth = array())
    {
	    $customFields = json_decode( $this->api->getCustomFields( $listId ) );

	    return $customFields;
    }

    /**
     *
     * @param $listId the id for which you want to get segments for
     * @param array $auth override the class authentication credentials
     * @return mixed|null segments list
     */
    public function get_segments($listId, $getDetails = false, $auth = array()) {

	    $segments = $this->api->getSegments( $listId );

	    if ( ! empty( $segments ) ) {
		    $segments = json_decode( $segments );

		    if ( $getDetails ) {
			    foreach ( $segments as $segment ) {
				    $details = $this->api->getSegmentDetails( $segment->SegmentID );
				    if ( null !== $details ) {
					    $segment->Details = $details;
				    }
			    }
			    return $segments;
		    }

		    return $segments;

	    }
	    return null;
    }
    /**
     *
     * @param $listId the id for which you want to get segments for
     * @param array $auth override the class authentication credentials
     * @return mixed|null segments list
     */
    public function get_segment($segmentID, $auth = array())
    {
	    $segment = $this->api->getSegmentDetails( $segmentID );
        return $segment !== null ? json_decode($segment) : $segment;
    }

}
