<?php

namespace forms\core;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Helper
 * @package core
 */
abstract class Helper {

    /***
     * @param $data
     * @param bool $dump
     */
    public static function display($data, $dump = false){
        echo "<pre>";
        if ($dump){
            var_dump($data);
        } else {
            print_r($data);
        }
        echo "</pre>";
    }

    public static function tokenize($string, $glue = '-', $toUpper = false){

        $token = "forms{$glue}for{$glue}campaign{$glue}monitor{$glue}". $string;
        if ($toUpper) {
            $token = ucwords( $token );
        }
        return $token;
    }

    /**
     * @return string
     */
    public static function getActionUrl(){
        return get_admin_url() . 'admin.php?page=campaign-monitor-for-wordpress';
    }

    /**
     * @return string
     */
    public static function getCampaignMonitorPermissions()
    {
        $permissions = array("ViewReports", "ViewSubscribersInReports",
            "ManageLists", "ImportSubscribers", "AdministerAccount");

        return implode(',', $permissions);
    }

    /**
     * @return string
     */
    public static function getRedirectUrl($isConnected = 'true'){
        return get_admin_url() . 'admin.php?page=campaign-monitor-for-wordpress&connected=' . $isConnected;
    }
    /**
     * @param string $file
     * @param bool $url
     * @return string
     */
    public static function getPluginDirectory($file = '', $url = false){
        if (empty($file)){
            return Config::getRoot();
        } else {
            return Config::getRoot() . $file;
        }
    }


    public static function getPages()
    {
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'post_type' => 'page',
            'post_status' => 'publish' );
        $items = get_pages( $args );

        $availableItems = array();
        if (!empty( $items )) {
            foreach ($items as $item) {
                $availableItems[$item->ID] = $item->post_title;
            }
        }

        return $availableItems;
    }

    public static function getPosts()
    {
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'post_type' => 'posts',
            'post_status' => 'publish' );
        $items = get_pages( $args );

        $availableItems = array();
        if (!empty( $items )) {
            foreach ($items as $item) {
                $availableItems[$item->ID] = $item->post_title;
            }
        }

        return $availableItems;
    }

    public static function generateModal( $body, $title = '' )
    {
        $html = '<div id="TB_overlay" class="TB_overlayBG"></div>
        <div id="TB_window" class="thickbox-loading" style="visibility: visible" >
            <div id="TB_title">
            <div id="TB_ajaxWindowTitle">
                '.filter_var($title, FILTER_SANITIZE_STRING).'
             </div>
             <div id="TB_closeAjaxWindow"><button type="button" id="TB_closeWindowButton">
                        <span class="screen-reader-text">Close</span><span class="tb-close-icon">
                        </span></button></div></div>
            <div id="TB_ajaxContent">
            '.filter_var($body, FILTER_SANITIZE_STRING).'
            </div>
        </div>
        ';

        echo $html;
    }

    public static function getString($text, $strip=false, $translate=false)
    {
        if ($strip)
        {
            $text=preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $text);
            $text=strip_tags($text);
        }

        if ($translate)
        {
            if (function_exists('__'))
            {
                $text=__($text);
            }
        }

        $text=htmlentities($text);

        return $text;
    }

    public static function toHtml($text, $strip=false, $translate=false)
    {
        echo self::getString($text, $strip, $translate);
    }


}