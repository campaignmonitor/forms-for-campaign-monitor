<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CampaignMonitorAdminSettings extends CampaignMonitorBase {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function add_admin_menu() {

		add_menu_page(
			'Campaign Monitor',
			'Campaign <br/> Monitor',
			'manage_options',
			'campaign-monitor-options',
			array( $this, 'options_page' ),
            plugin_dir_url( __FILE__ ) . '../img/cm-adm-icon.svg'
		);

		add_submenu_page(
			'campaign-monitor-options',
			'Campaign <br/>Monitor',
			__('Settings', 'campaign-monitor'),
			'manage_options',
			'campaign-monitor-options',
			array( $this, 'options_page' )
		);

		add_submenu_page(
			'',
			'campaign-monitor-options',
			'Log Out Campaign Monitor',
			'manage_options',
			'campaign-monitor-logout',
			array( $this, 'logout_page' )
		);
	}


	function settings_init() {
			register_setting( 'campaign-monitor-options', 'campaign_monitor_settings' );

			add_settings_section(
				'campaign-monitor_section',
				__( 'Campaign Monitor API', 'campaign-monitor' ),
				array( $this, 'section_callback' ),
				'campaign-monitor-options'
			);

			add_settings_field(
				'campaign-monitor_api_key',
				__( 'API Key', 'campaign-monitor' ),
				array( $this, 'api_key_render' ),
				'campaign-monitor-options',
				'campaign-monitor_section'
			);
	}


	function api_key_render() {

		$this->renderTemplate('admin/partials/form-api_key');

	}


	function section_callback() {

		echo __( 'Please enter your API Key from Campaign Monitor below. More information on where to find your API key can be found in our <a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206">help documentation</a>.', 'campaign-monitor' );

	}


	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$this->renderTemplate('admin/options-form');
	}

	public function logout_page() {
		if ( isset($_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-logout' ) ) {
				CampaignMonitorPluginInstance()->remove_options();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-options" ) );
			}
		}
		$this->renderTemplate('admin/logout');
	}
}