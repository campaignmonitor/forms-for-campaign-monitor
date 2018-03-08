<?php
namespace forms\core;

class CampaignMonitor
{
    protected $auth = array();
    protected static $errors = array();

    public function __construct($accessToken, $refreshToken)
    {
        $this->auth = array('access_token' => $accessToken,
            'refresh_token' => $refreshToken);
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
    public function refresh_token($auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_general.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_General($auth);
        return $instance->refresh_token();
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_clients($auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_general.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_General($auth);
        $result = $instance->get_clients();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            self::$errors[] = $result->response;

            Log::write( $result );
            return $result->response;

           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function instantiate_url($integrationKey, $uri, $scope, $state = false)
    {

        $params["integration_key"] = $integrationKey;
        $params["domain_uri"] = $uri;
        $params["scope"] = $scope;
        if ($state) {
            $params["state"] = $state;
        }

        $url = http_build_query($params);

        return 'https://api.createsend.com/oauth' . '?' . $url ;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function authorize_url( $client_id, $redirect_uri, $scope)
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_general.php');
        require_once $clientsClass;


        return \CS_REST_General::authorize_url($client_id, $redirect_uri , $scope );

    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of timezones
     */
    public function get_timezones($auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_general.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_General($auth);
        $result = $instance->get_timezones();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            self::$errors[] = $result->response;

            Log::write( $result );
            return $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
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

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_clients.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Clients(NULL, $auth);
        $result = $instance->create($clientObject);

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            self::$errors[] = $result->response;
            Log::write( $result );
            return $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_subscriber($listId, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_subscribers.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Subscribers($listId, $auth);
        $result = $instance->get();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception

           self::$errors[] = $result->response;
            Log::write( $result );
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_stats($listId, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Lists($listId, $auth);
        $result = $instance->get_stats();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception

           self::$errors[] = $result->response;
            Log::write( $result );
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_list_details($listId, $auth = array())
    {
        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Lists($listId, $auth);
        $result = $instance->get();

        if ($result->was_successful()) {
             return $result->response;
        } else {
           Log::write($result);
           self::$errors[] = $result->response;
        }

        return null;
    }

    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function send_email($to, $listName, $message = array(), $auth = array())
    {


        $postUrl = 'https://integrationstore-5d74b11ccbdb8fa8.microservice.createsend.com/campaign-monitor-for-woo-commerce/email/data-synced';

        $data = array();
        $data['ToEmail'] = $to;
        $data['ListName'] = $listName;
        $data['SubscriberCount'] = $message['subscribers_count'];
        $options = array("type" => "json");
        $headers = array('Authorization: Bearer ' . Settings::get('access_token'), 'Content-Type: application/json', 'Cache-Control: no-cache');

        Connect::request($data, $postUrl, $options, $headers );
        return;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function import_subscribers($listId, $data, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_subscribers.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Subscribers($listId, $auth);
        $resubscribe = false;
        $queueSubscriptionBasedAutoResponders = true;
        $restartSubscriptionBasedAutoResponders = false;
        $result = $instance->import($data, $resubscribe, $queueSubscriptionBasedAutoResponders, $restartSubscriptionBasedAutoResponders);

        if ($result->was_successful()) {
             return $result->response;
        } else {

           Log::write($result);
           self::$errors[] = $result->response;

        }

        return null;
    }
    /**
     * @param array $auth override the class authentication credentials
     * @return mixed
     */
    public function add_subscriber($listId, $data, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_subscribers.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Subscribers($listId, $auth);
        $result = $instance->add($data);

        if ($result->was_successful()) {
             return $result->response;
        } else {

           Log::write($result);
           self::$errors[] = $result->response;

        }

        return null;
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

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_segments.php');
        require_once $clientsClass;


        if (empty($auth)) {
            $auth = $this->auth;
        }

//        $segmentOptions = array('Title' => $name,'RuleGroups' => array());

        $instance = new \CS_REST_Segments(NULL, $auth);


        $result = $instance->create($listId, $segment);

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            Log::write($result);
            self::$errors[] = $result->response;
            return $result->response;
        }

        return null;
    }

    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function update_custom_field($listId,$fieldKey, $fieldName, $visibleInPreferenceCenter = true, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        $instance = new \CS_REST_Lists($listId, $auth);
        $params = array(
            'FieldName' => $fieldName,
            'VisibleInPreferenceCenter'=> $visibleInPreferenceCenter
        );
        $result = $instance->update_custom_field($fieldKey, $params);

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            $result->response->name = $fieldName;
            Log::write($result);
           self::$errors[] = $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }
    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function create_custom_field($listId,$fieldName, $dataType, $options = array(), $visibleInPreferenceCenter = true, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        Log::write( $auth );

        $instance = new \CS_REST_Lists($listId, $auth);
        $params = array(
            'FieldName' => $fieldName,
            'DataType' => $dataType,
            'Options' => $options,
            'VisibleInPreferenceCenter'=> $visibleInPreferenceCenter
        );
        $result = $instance->create_custom_field($params);

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            $result->response->name = $fieldName;
            Log::write($result);
           self::$errors[] = $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return $result;
    }

    /**
     * @param $clientId for which client to get the lists
     * @param array $auth override the class authentication credentials
     * @return mixed|null list of clients
     */
    public function get_client_list($clientId, $auth = array())
    {

        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_clients.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        $clients = new \CS_REST_Clients($clientId, $auth);
        $result = $clients->get_lists();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            Log::write($result);
           self::$errors[] = $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }

    /**
     * @param $clientId for which to whom create the list
     * @param array $auth override the class authentication credentials
     * @return mixed|null list id
     */
    public function create_list($clientId, $listTitle, $confirmedOptIn = false, $unsubscribePage = '', $confirmationPage = '', $auth = array())
    {
            $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
            require_once $clientsClass;
            $requestResults = new \stdClass();

            if (empty($auth)) {
                $auth = $this->auth;
            }

            $instance = new \CS_REST_Lists(NULL, $auth);


            $listOptions = array(
                'Title' => $listTitle,
                'UnsubscribePage' => $unsubscribePage,
                'ConfirmedOptIn' => $confirmedOptIn,
                'ConfirmationSuccessPage' => $confirmationPage,
                'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS
            );

            $result = $instance->post_request($instance->_base_route . 'lists/' . $clientId . '.json', $listOptions);

            if ($result->was_successful()) {
                return $result->response;
            } else {
                self::$errors[] = $result->response;
                Log::write($result);
                return  $result->response;
            }

        return null;
    }

    /**
     *
     * @param $listId the id for which you want the custom fields from
     * @param array $auth override the class authentication credentials
     * @return mixed|null custom field list
     */
    public function get_custom_fields($listId, $auth = array())
    {
        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        $customFields = new \CS_REST_Lists($listId, $auth );
        $result = $customFields->get_custom_fields();

        if ($result->was_successful()) {
             return $result->response;
        } else {
            // TODO log exception
            Log::write($result);
           self::$errors[] = $result->response;
           //$requestResults->status_code = $result->http_status_code;
        }

        return null;
    }

    /**
     *
     * @param $listId the id for which you want to get segments for
     * @param array $auth override the class authentication credentials
     * @return mixed|null segments list
     */
    public function get_segments($listId, $getDetails = false, $auth = array())
    {
        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_lists.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        $customFields = new \CS_REST_Lists($listId, $auth );
        $result = $customFields->get_segments();

        if ($result->was_successful()) {
              $segments = $result->response;

            if (!empty($segments)){
                if ($getDetails){
                    foreach ($segments as $segment){
                        $details = $this->get_segment($segment->SegmentID);
                        if (null != $details){
                            $segment->Details = $details;
                        }
                    }
                    return $segments;
                } else {
                    return $segments;
                }
            }
        } else {
            // TODO log exception
           self::$errors[] = "Error trying to get segments: ". $result->response;
            Log::write( $result );
           //$requestResults->status_code = $result->http_status_code;
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
        $clientsClass = Helper::getPluginDirectory('campaign-monitor/csrest_segments.php');
        require_once $clientsClass;
        $requestResults = new \stdClass();

        if (empty($auth)) {
            $auth = $this->auth;
        }

        $segmentInstance = new \CS_REST_Segments($segmentID, $auth);
        $result = $segmentInstance->get();

        if ($result->was_successful()) {
            $segment = $result->response;
            return $segment;
        } else {
            // TODO log exception
            $message = "Error trying to get segment with id:$segmentID  ". print_r($result->response, true);
           self::$errors[] = $message;
            Log::write($message);
        }

        return null;
    }


    function get_list_segments($listId, $include_details=0)
    {
        $wrap = $this->get_wrap_list($listId);
        $result = $wrap->get_segments();
        $return_val = $this->process_api_return($result);


        if ($include_details && is_array($return_val))
        {
            foreach ($return_val as $k=>$seg)
            {
                $result=$this->get_segment_details($seg->SegmentID);
                if (is_object($result))
                {
                    if (!empty($result->SegmentID))
                    {
                        $return_val[$k]->details = $result;
                    }
                }
            }
        }

        return $return_val;
    }
}
