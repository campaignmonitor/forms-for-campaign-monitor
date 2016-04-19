<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'wp_tables/campaign_monitor_wp_table_lists.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_list.php';

class CampaignMonitorAdminLists extends CampaignMonitorBase {

	private $tableName = 'cm_lists';

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {

		$hook = add_submenu_page(
			'campaign-monitor-options',
			'Campaign Monitor Lists',
			'Lists',
			'manage_options',
			'campaign-monitor-lists',
			array( $this, 'lists_page' )
		);
		add_action( "load-$hook", array( $this, 'add_options' ) );

		$hook = add_submenu_page(
			'campaign-monitor-lists',
			'Campaign Monitor Lists',
			'Import List',
			'manage_options',
			'campaign-monitor-import-list',
			array( $this, 'import_list_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-lists',
			'Campaign Monitor Lists',
			'Add List',
			'manage_options',
			'campaign-monitor-add-list',
			array( $this, 'add_list_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-lists',
			'Campaign Monitor Lists',
			'Edit List',
			'manage_options',
			'campaign-monitor-edit-list',
			array( $this, 'edit_list_page' )
		);

		$hook = add_submenu_page(
			'campaign-monitor-lists',
			'Campaign Monitor Lists',
			'Edit List',
			'manage_options',
			'campaign-monitor-edit-list-fields',
			array( $this, 'edit_list_fields_page' )
		);


	}

	function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'Lists',
			'default' => 10,
			'option' => 'lists_per_page'
		);
		add_screen_option( $option, $args );
	}


	public function lists_page() {
		$this->renderTemplate('admin/lists-wp_table');
	}

	public function import_list_page() {
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) && isset( $_POST['_wp_http_referer'] ) && ! empty( $_POST['_wp_http_referer'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-import-list' ) ) {
				$response = CampaignMonitorPluginInstance()->connection->get_list( $_POST['id'] );
				if ( 200 != $response->http_status_code ) {
					CampaignMonitorPluginInstance()->connection->last_error = $response->response;
				} else {
					if ( ! $this->add_list( $response->response->Title, $response->response->ListID ) ) {
						CampaignMonitorPluginInstance()->connection->last_error = (object)['Message' => __( 'List already added', 'campaign-monitor' )];
					} else {
						wp_redirect( admin_url("admin.php?page=campaign-monitor-lists") );
					}
				}
			} else {
				wp_nonce_ays( 'campaign-monitor-import-list' );
			}
		}
		$this->renderTemplate("admin/lists-import");
	}

	public function add_list( $title, $id ) {
		global $wpdb;
		if ( $id != null && ! $this->exists( $id ) ) {
			$wpdb->insert($wpdb->prefix.$this->tableName, array(
				'id'   => $id,
				'name' => $title
			));

			return true;
		}

		return false;
	}

	public function get_registered_lists( $orderBy = null ) {

		global $wpdb;

		$sql = 'SELECT * FROM `'.$wpdb->prefix.$this->tableName.'`';

		if(!empty($orderBy))
		{
			$sql .= ' ORDER BY ' . $orderBy;
		}

		$all = $wpdb->get_results($sql);

		return $all;
	}

	public function prepare_data( $data ) {
		if ( array_key_exists( 'ConfirmedOptIn', $data ) ) {
			$data['ConfirmedOptIn'] = 1==$data['ConfirmedOptIn'];
		}
		return $data;
	}

	public function exists( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM " . $wpdb->prefix . $this->tableName . " WHERE id = '" . sanitize_text_field( $id ) ."'";
		$list = $wpdb->get_results($sql);

		return (count($list) > 0)?true:false;
	}

	public function add_list_page() {
		if ( isset( $_POST['title'] ) && ! empty( $_POST['title'] ) && isset( $_POST['_wp_http_referer'] ) && ! empty( $_POST['_wp_http_referer'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-add-list' ) ) {
				$response = CampaignMonitorPluginInstance()->connection->save_list( $_POST['client'], $this->prepare_data( $_POST ) );
				if ( 201 != $response->http_status_code ) {
					CampaignMonitorPluginInstance()->connection->last_error = $response->response;
				} else {
					$response = CampaignMonitorPluginInstance()->connection->get_list( $response->response );
					if ( ! $this->add_list( $response->response->Title, $response->response->ListID ) ) {
						CampaignMonitorPluginInstance()->connection->last_error = (object)['Message' => __( 'List already added', 'campaign-monitor' )];
					} else {
						wp_redirect( admin_url("admin.php?page=campaign-monitor-lists") );
					}
				}

			} else {
				wp_nonce_ays( 'campaign-monitor-add-list' );
			}
		}

		$this->renderTemplate('admin/lists-add');
	}

	public function edit_list_page() {
		if ( ( ! isset( $_GET['list'] ) && empty( $_GET['list'] ) ) || ! $this->exists( $_GET['list'] ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-lists") );
		}
		if ( isset( $_POST['title'] ) && ! empty( $_POST['title'] ) && isset( $_POST['_wp_http_referer'] ) && ! empty( $_POST['_wp_http_referer'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-list' ) ) {
				$response = CampaignMonitorPluginInstance()->connection->update_list( $_GET['list'], $this->prepare_data( $_POST ) );
				if ( 200 != $response->http_status_code ) {
					CampaignMonitorPluginInstance()->connection->last_error = $response->response;
				} else {
					$response = CampaignMonitorPluginInstance()->connection->get_list( $_GET['list'] );
					$list = new CampaignMonitorList();
					$list->load( $_GET['list'] );
					$list->name = $response->response->Title;
					$list->comments = ( array_key_exists( 'comments', $_POST ) && 1 == $_POST['comments'] ) ? 1 : 0;
					$list->comments_text = $_POST['comments_text'];
					$list->registration = ( array_key_exists( 'registration', $_POST ) && 1 == $_POST['registration'] ) ? 1 : 0;
					$list->registration_text = $_POST['registration_text'];
					$list->save();
					wp_redirect( admin_url("admin.php?page=campaign-monitor-lists") );
				}
			} else {
				wp_nonce_ays( 'campaign-monitor-add-list' );
			}
		}
		$this->renderTemplate('admin/lists-edit');
	}

	public function edit_list_fields_page() {
		if ( ( ! isset( $_GET['list'] ) && empty( $_GET['list'] ) ) || ! $this->exists( $_GET['list'] ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-lists") );
		}
		if ( isset( $_POST['_wp_http_referer'] ) && ! empty( $_POST['_wp_http_referer'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-list-fields' ) ) {
				foreach( $_POST['fields'] as $k => $field ) {
					$field['FieldName'] = stripslashes($field['FieldName']);
					if ( is_numeric( $k ) ) {
						CampaignMonitorPluginInstance()->connection->add_list_fields( $_GET['list'], $field );
					} else {
						CampaignMonitorPluginInstance()->connection->update_list_fields( $_GET['list'], "[$k]", $field );
					}
				}

				if ( isset( $_POST['toDelete'] ) ) {
					foreach( $_POST['toDelete'] as $d ) {
						CampaignMonitorPluginInstance()->connection->delete_list_fields( $_GET['list'], $d );
					}
				}
			} else {
				wp_nonce_ays( 'campaign-monitor-edit-list-fields' );
			}
		}

		$this->renderTemplate('admin/lists-fields');
	}

}