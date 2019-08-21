<?php

namespace forms\core;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Settings
 * @package core
 *
 * Holds settings for the app
 */
class Settings {

    protected static $settings = array();
    const name = 'campaign_monitor_forms_account_settings';

    /**
     * Add a setting
     * 
     * @param $setting
     * @param $value
     */
    public static function add($setting, $value)
    {
        self::$settings = Options::getArray(self::name);
        self::$settings[$setting] = $value;
       Options::update(self::name, self::$settings);
    }

    public static function clear(){
        return Options::update(self::name, []);
    }

    /**
     * get a specific settings or all of them if no argument is provided
     * 
     * @param string $setting
     * @return array | null
     */
    public static function get($setting = ''){
        if (null == $setting){
            return Options::getArray(self::name);
        }else {
            $settings = Options::getArray(self::name);
            if (!empty($settings)){
                if (array_key_exists($setting, $settings)){
                    return $settings[$setting];
                }
            }
        }

        return null;
    }
}