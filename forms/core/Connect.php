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
            $headers = array('contentType' => 'application/x-www-form-urlencoded');
        }



        $result = $client->request($postUrl, $dataToPost, 'POST', $headers);
        return $result;
    }

}