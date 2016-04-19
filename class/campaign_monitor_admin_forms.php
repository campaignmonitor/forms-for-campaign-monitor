<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'wp_tables/campaign_monitor_wp_table_forms.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_form.php';

class CampaignMonitorAdminForms extends CampaignMonitorBase {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {

		$hook = add_submenu_page(
			'campaign-monitor-options',
			'Campaign Monitor Forms',
			'Forms',
			'manage_options',
			'campaign-monitor-forms',
			array( $this, 'forms_page' )
		);
		add_action( "load-$hook", array( $this, 'add_options' ) );

		$hook = add_submenu_page(
			'campaign-monitor-forms',
			'Campaign Monitor Forms',
			'Add Form',
			'manage_options',
			'campaign-monitor-add-form',
			array( $this, 'add_form_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-forms',
			'Campaign Monitor Forms',
			'Edit Form',
			'manage_options',
			'campaign-monitor-edit-form',
			array( $this, 'edit_form_page' )
		);
        
	}

	function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'Forms',
			'default' => 10,
			'option' => 'forms_per_page'
		);
		add_screen_option( $option, $args );
	}


	public function forms_page() {
		$this->renderTemplate('admin/forms-wp_table');
	}

	public function add_form_page() {

		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-add-form' ) ) {
				$form = new CampaignMonitorForm();
				$form->name = sanitize_text_field( $_POST['title'] );
				$form->data['list_id'] = sanitize_text_field( $_POST['list_id'] );
				$form->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-edit-form&form=".$form->id ) );
                

			} else {
				wp_nonce_ays( 'campaign-monitor-add-form' );
			}
		}

		$this->renderTemplate('admin/forms-add');
	}

	public function edit_form_page() {
		$form = new CampaignMonitorForm();
		if ( ( ! isset( $_GET['form'] ) && empty( $_GET['form'] ) ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );
		}
		$form->load($_GET['form']);
		if ( 0 == $form->id ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );
		}
		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-form' ) ) {
				$form->name = sanitize_text_field( $_POST['title'] );
				$form->data['submitText'] = sanitize_text_field( $_POST['submitText'] );
				$form->data['fields'] = $_POST['fields'];
				$form->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );

			} else {
				wp_nonce_ays( 'campaign-monitor-edit-form' );
			}
		}
		$this->renderTemplate('admin/forms-edit');
	}

}