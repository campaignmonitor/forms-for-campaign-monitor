<?php

namespace forms\core;

class Request {


    public static function getPost($name = ''){
        if (empty($name)) {
            return \stripslashes_deep($_POST);
        }
        return (isset($_POST[$name])) ? \stripslashes_deep($_POST[$name]) : null;
    }

    public static function get($name = '', $raw = false){

        if (empty($name)) {
            return \stripslashes_deep($_GET);
        }

        return (isset($_GET[$name])) ? \stripslashes_deep($_GET[$name]) : null;
    }



    /**
     * @param $name
     * @return request name null otherwise
     */
    public static function getParam($name = ''){

        if (isset($_POST[$name]))  return \stripslashes_deep($_POST[$name]);

        if (isset($_GET[$name])) return \stripslashes_deep($_GET[$name]);

        if (empty( $name )) {
            return \stripslashes_deep($_REQUEST);
        }
        return null;
    }
}

