<?php

namespace forms\core;

class Connect
{

    protected static $transport = 'https://api.createsend.com';
    protected static $connection = null;

    public static function getTransport($endpoint, $params = array())
    {

        if (empty($params)) return self::$transport;

        $pieces = explode('/', $endpoint);
        $urlParms = http_build_query($params);
        return self::$transport .= '/' . implode('/', $pieces) . '/?' . $urlParms;
    }

    public static function Authenticate($credentials)
    {

    }

    public static function create($params)
    {

    }

	/**
	 * @param $data
	 * @param string $endpoint
	 * @param array $options
	 * @param array $headers
	 * @deprecated This function will be removed in the next release
	 */
    public static function request($data, $endpoint = '', $options = array("type" => "urlencode"), $headers = array())
    {

        $client = new HttpClient();

        if (empty($endpoint)) {
            $postUrl = self::$transport;
        } else {
            $postUrl = $endpoint;
        }


        $dataToPost = '';

        if (is_array($data) || is_object($data)) {
            $dataToPost = json_encode($data);
        } else {
            $dataToPost = $data;
        }


        if (array_key_exists('type', $options)){
            switch ($options['type']){
                case "urlencode":
                    $dataToPost =  http_build_query($data);
                    break;
                case "json" :
                    $dataToPost =  json_encode($data);
                    break;
            }
        }


        if (empty($headers)){
            $headers = array(
            	'contentType' => 'application/x-www-form-urlencoded',
	            'X-Forwarded-For' => self::getRemoteUserIp(),
            );
        }


        $result = $client->request($postUrl, $dataToPost, 'POST', $headers);
        return $result;
    }

	private static function getRemoteUserIp() {
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

}