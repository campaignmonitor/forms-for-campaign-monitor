<?php

namespace forms\core;

class View {

    public $name;

    protected $_properties = array();

    public function __call($method, $params)
    {
        $methodName = substr( $method, 3 );

        if (strpos( $method, "get" ) !== FALSE && array_key_exists( $methodName, $this->_properties )) {
            return $this->_properties[$methodName];
        }
        if (strpos( $method, "set" ) !== FALSE) {
            $this->_properties[$methodName] = $params[0];

        } else {
            if (isset($this->$method)) {
                $func = $this->$method;
                return call_user_func_array($func, $params);
            }
        }


    }

    public function render( $view , $screen = 'admin')
    {
        $location = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $screen;
        $filePath = $location . DIRECTORY_SEPARATOR . $view . '.php';

        if (file_exists( $filePath )) {
            include_once $filePath;
        } else {
            throw new \Exception( "Couldn't load file: $filePath" );
        }
    }

}