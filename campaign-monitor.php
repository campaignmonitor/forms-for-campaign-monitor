<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Plugin Name: Campaign Monitor for WordPress
 * Plugin URI: http://campaignmonitor.com
 * Description: Manage Campaign Monitor Lists, Custom Fields and add forms and how you show them to your users..
 * Version: 2.8.11
 * Author: Campaign Monitor
 * Author URI: http://campaignmonitor.com
 * License: License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
$version = (float)phpversion();
if($version < 5.3) {
    if (array_key_exists('activate', $_GET)) {
        unset($_GET['activate']);
    }
    add_action('admin_notices', function() {
        $html = '<div id="message" class="error notice is-dismissible">';
        $html .= '<p>';
        $html .= __(' Campaign Monitor requires at least PHP Version 5.3.0, version: '.phpversion().' detected', 'campaign-monitor');
        $html .= '</p>';
        $html .= '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        $html .= '</div><!-- /.updated -->';
        echo $html;
        deactivate_plugins( __FILE__, true);
    });
} else {

    spl_autoload_register(function ($class_name) {

        $location = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name)  . '.php';
        if (file_exists($location)) {
            try{
                require_once $location;
                return;
            } catch(Exception $e){
                throw new Exception($e->getMessage());
            }
        }
        else
        {
            $location = __DIR__ . DIRECTORY_SEPARATOR . 'forms'. DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name)  . '.php';
            if (file_exists($location)) {
                try{
                    require_once $location;
                    return;
                } catch(Exception $e){
                    throw new Exception($e->getMessage().'  (CM API not found)');
                }
            }
        }
    });



    add_action('plugins_loaded', function(){

        \forms\core\Application::run();
    });


    add_action('plugins_loaded', function(){
        $langLocation =  dirname( plugin_basename( __FILE__ ) ). '/forms/core/lang';
       $didTranslationsLoaded = load_plugin_textdomain( 'campaign-monitor-forms', false, $langLocation );

    });


}
