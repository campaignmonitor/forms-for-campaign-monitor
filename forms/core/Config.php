<?php

namespace forms\core;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Config
{

    public static function getRoot($directory = '', $returnUri  = FALSE)
    {
        if ($returnUri) {
            return  dirname( plugin_dir_url( __DIR__  )) . '/' . $directory;
        }

        return dirname( plugin_dir_path(__DIR__) ) . DIRECTORY_SEPARATOR . $directory;
    }

    public static function getName()
    {
        return 'forms_for_campaign_monitor';
    }

}