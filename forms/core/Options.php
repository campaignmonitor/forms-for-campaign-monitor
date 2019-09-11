<?php

namespace forms\core;

class Options extends Config
{

    /**
     * wordpress option wrapper
     *
     * @param $name
     * @param string $value
     * @param string $deprecated
     * @param string $autoload
     */
    public static function add($name, $value = '', $deprecated = '', $autoload = 'yes' ){
        $optionName = self::getName() . '_' . $name;
        add_option($optionName, $value, $deprecated, $autoload  );
    }

    /**
     * wordpress option wrapper
     *
     * @param $name
     * @param $value
     * @param null $autoload
     * @return bool
     */
    public static function update( $name, $value, $autoload = null ){
        $optionName = self::getName() . '_' . $name;
        return update_option($optionName, $value, $autoload);
    }

    /**
     * wrapper for the wordpress options
     *
     * @param $name
     * @param array $default
     * @return mixed|void
     */
    public static function getArray( $name, $default = []){
        $optionName = self::getName() . '_' . $name;
        $option = get_option($optionName,$default);
        if (!is_array($option)) {            
            $option = [];
        }
        return $option;
    }

    /**
     * wrapper for the wordpress options
     *
     * @param $name
     * @param bool $mixed
     * @return mixed|void
     */
    public static function get( $name, $mixed = false){
        $optionName = self::getName() . '_' . $name;
        $option = get_option($optionName,$mixed);
        return $option;
    }

    /**
     * @param $name
     */
    public static function delete($name){
        $optionName = self::getName() . '_' . $name;
        delete_option($optionName);
    }
}