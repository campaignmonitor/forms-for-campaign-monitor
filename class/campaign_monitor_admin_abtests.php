<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'wp_tables/campaign_monitor_wp_table_abtests.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_element.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_button.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_slider.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_bar.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_lightbox.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_simple_form.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_abtest.php';

class CampaignMonitorAdminABTests extends CampaignMonitorBase {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {

		$hook = add_submenu_page(
			'campaign-monitor-forms',
			'Campaign Monitor Elements',
			'A/B Testing',
			'manage_options',
			'campaign-monitor-abtests',
			array( $this, 'abtests_page' )
		);
		add_action( "load-$hook", array( $this, 'add_options' ) );

		$hook = add_submenu_page(
			'campaign-monitor-elements',
			'Campaign Monitor Elements',
			'Add A/B test',
			'manage_options',
			'campaign-monitor-add-abtest',
			array( $this, 'add_abtest_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-elements',
			'Campaign Monitor Elements',
			'Edit A/B Test',
			'manage_options',
			'campaign-monitor-edit-abtest',
			array( $this, 'edit_abtest_page' )
		);
        
        add_submenu_page(
			'campaign-monitor-forms',
			'Campaign <br/>Monitor',
			__('Settings', 'campaign-monitor'),
			'manage_options',
			'campaign-monitor-options'
		);
	}

	function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'A/B Tests',
			'default' => 10,
			'option' => 'abtests_per_page'
		);
		add_screen_option( $option, $args );
	}


	public function abtests_page() {
		$this->renderTemplate('admin/abtests-wp_table');
	}

	public function add_abtest_page() {

		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-add-abtest' ) ) {
				$abtest = new CampaignMonitorABTest();
				$abtest->name = sanitize_text_field( $_POST['title'] );
				$abtest->type = sanitize_text_field( $_POST['type'] );
				$abtest->data['first_element'] = intval( $_POST['first_element'] );
				$abtest->data['second_element'] = intval( $_POST['second_element'] );
				$abtest->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-edit-abtest&e=".$abtest->id ) );

			} else {
				wp_nonce_ays( 'campaign-monitor-add-abtest' );
			}
		}

		$this->renderTemplate('admin/abtests-add');
	}

	public function edit_abtest_page() {
		$abtest = new CampaignMonitorABTest();
		if ( ( ! isset( $_GET['e'] ) && empty( $_GET['e'] ) ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-abtests") );
		}
		$abtest->load($_GET['e']);
		if ( 0 == $abtest->id ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-abtests") );
		}
		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-abtest' ) ) {
				$abtest->name = sanitize_text_field( $_POST['title'] );
				$abtest->enabled = isset( $_POST['enabled'] )? $_POST['enabled'] : 0;
				$fields = $_POST['fields'];
				if ( array_key_exists( 'show_in', $fields ) ) {
					$fields['show_in'] = $_POST['fields']['show_in'];
				} else {
					$fields['show_in'] = array();
				}

				$abtest->data = array_merge( $abtest->data, $fields );
				$abtest->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-abtests") );

			} else {
				wp_nonce_ays( 'campaign-monitor-edit-abtest' );
			}
		}
		$this->renderTemplate('admin/abtests-edit');
	}

	private function process_show_in( $urls ) {
		$urls = explode("\r\n", $urls );
		$values = array();
		foreach ( $urls as $url ) {
			if ( ! empty( $url  ) ) {
				$values[] = $url;
			}
		}

		return $values;
	}

}