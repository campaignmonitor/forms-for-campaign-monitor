<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Plugin Name: Campaign Monitor
 * Plugin URI: http://campaignmonitor.com
 * Description: Manage Campaign Monitor Lists, Custom Fields and add forms and how you show them to your users..
 * Version: 1.5
 * Author: Campaign Monitor
 * Author URI: http://campaignmonitor.com
 * License: License: GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
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

define( 'CAMPAIGN_MONITOR_CLASS_FOLDER', plugin_dir_path(__FILE__) . 'class/' );
define( 'CAMPAIGN_MONITOR_TEMPLATES_FOLDER', plugin_dir_path(__FILE__) . 'templates/' );
define( 'CAMPAIGN_MONITOR_CREATESEND_FOLDER', plugin_dir_path(__FILE__) . 'createsend-php/' );
define( 'CAMPAIGN_MONITOR_PLUGIN_URL', plugins_url('/', __FILE__) );


require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . "campaign_monitor.php" );
require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . "campaign_monitor_install.php" );
require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . "virtual_pages.php" );

$install = new CampaignMonitorInstall();

/**
 * Returns the main instance of CampaignMonitor to prevent the need to use globals.
 *
 * @since  2.1
 * @return CampaignMonitor
 */
function CampaignMonitorPluginInstance() {
	return CampaignMonitor::instance();
}

// Global for backwards compatibility.
$GLOBALS['campaignmonitor'] = CampaignMonitorPluginInstance();

CampaignMonitorPluginInstance()->init();
$i = new CampaignMonitorInstall();
$i->install();
