<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'wp_tables/campaign_monitor_wp_table_elements.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_element.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_button.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_slider.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_bar.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_lightbox.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_simple_form.php';

class CampaignMonitorAdminElements extends CampaignMonitorBase {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {

		$hook = add_submenu_page(
			'campaign-monitor-options',
			'Campaign Monitor Elements',
			'Elements',
			'manage_options',
			'campaign-monitor-elements',
			array( $this, 'elements_page' )
		);
		add_action( "load-$hook", array( $this, 'add_options' ) );

		$hook = add_submenu_page(
			'campaign-monitor-elements',
			'Campaign Monitor Elements',
			'Add Element',
			'manage_options',
			'campaign-monitor-add-element',
			array( $this, 'add_element_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-elements',
			'Campaign Monitor Elements',
			'Edit Element',
			'manage_options',
			'campaign-monitor-edit-element',
			array( $this, 'edit_element_page' )
		);
	}

	function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'Elements',
			'default' => 10,
			'option' => 'elements_per_page'
		);
		add_screen_option( $option, $args );
	}


	public function elements_page() {
		$this->renderTemplate('admin/elements-wp_table');
	}

	public function add_element_page() {

		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-add-element' ) ) {
				$element = new CampaignMonitorElement();
				$element->name = sanitize_text_field( $_POST['title'] );
				$element->data['form_id'] = intval( $_POST['form_id'] );
				$element->type = sanitize_text_field( $_POST['type'] );
				$element->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-edit-element&e=".$element->id ) );

			} else {
				wp_nonce_ays( 'campaign-monitor-add-element' );
			}
		}

		$this->renderTemplate('admin/elements-add');
	}

	public function edit_element_page() {
		$element = new CampaignMonitorElement();
		if ( ( ! isset( $_GET['e'] ) && empty( $_GET['e'] ) ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-elements") );
		}
		$element->load($_GET['e']);
		if ( 0 == $element->id ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-elements") );
		}
		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-element' ) ) {
				$element->name = sanitize_text_field( $_POST['title'] );
				$element->global = intval( $_POST['global'] );
				$element->enabled = intval( $_POST['enabled'] );
				$fields = $_POST['fields'];
				if ( array_key_exists( 'show_in', $fields ) ) {
					if (  1 == $_POST['global'] ) {
						$fields['show_in'] = array();
					} else {
						$fields['show_in'] = $this->process_show_in( $_POST['fields']['show_in'] );
					}
				}
				$element->data = array_merge( $element->data, $fields );
				$element->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-elements") );

			} else {
				wp_nonce_ays( 'campaign-monitor-edit-element' );
			}
		}
		$this->renderTemplate('admin/'.$element->type.'s-edit');
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