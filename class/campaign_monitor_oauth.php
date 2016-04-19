<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CREATESEND_FOLDER . 'csrest_general.php';
require_once CAMPAIGN_MONITOR_CREATESEND_FOLDER . 'csrest_lists.php';
require_once CAMPAIGN_MONITOR_CREATESEND_FOLDER . 'csrest_clients.php';
require_once CAMPAIGN_MONITOR_CREATESEND_FOLDER . 'csrest_subscribers.php';


class CampaignMonitorOAuth extends CampaignMonitorBase {

	private $client_id;
	private $client_secret;
	private $api_key;
	private $redirect_uri;
	private $enabled = false;
	public  $last_error;

	function __construct() {
		$options = get_option( 'campaign_monitor_settings' );
		if ( isset( $options['api_key'] ) ) {
			$this->api_key = CampaignMonitorPluginInstance()->get_option('api_key');
			$this->enabled = true;
		}
		$this->redirect_uri = admin_url( '?page=campaign-monitor-oauth' );

		if( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_hooks' ) );
			add_action( 'init', array( $this, 'add_ob_start' ) );
			//add_action( 'admin_footer', array( $this, 'add_ob_end' ) );
		}
	}

	function authorize_url() {
		$authorize_url = CS_REST_General::authorize_url(
			$this->client_id,
			$this->redirect_uri,
			'ManageLists,ImportSubscribers',
			''
		);

		return $authorize_url;
	}

	function admin_hooks() {
		add_submenu_page(
			null,
			'Campaign Monitor OAuth',
			'',
			'manage_options',
			'campaign-monitor-oauth',
			array( $this, 'oauth_page' )
		);
	}

	function oauth_page() {
		$this->last_error = null;

		if ( isset( $_GET['code'] ) ) {
			$result = CS_REST_General::exchange_token(
				$this->client_id,
				$this->client_secret,
				$this->redirect_uri,
				$_GET['code']
			);

			if ($result->was_successful()) {
				CampaignMonitorPluginInstance()->save_option( 'access_token', $result->response->access_token );
				CampaignMonitorPluginInstance()->save_option( 'expires_in', $result->response->expires_in );
				CampaignMonitorPluginInstance()->save_option( 'refresh_token', $result->response->refresh_token );
				CampaignMonitorPluginInstance()->save_option( 'expire_date', $time = date("m/d/Y h:i:s a", time() + $result->response->expires_in) );

				wp_redirect( admin_url( '?page=campaign-monitor-options&oauth=complete' ) ); exit;
			} else {
				$this->last_error = array( 'error' => $result->response->error , 'error_description' => $result->response->error_description );
			}
		} else {
			$this->last_error = array( 'error' => sanitize_text_field( $_GET['error'] ) , 'error_description' => sanitize_text_field( $_GET['error_description'] ) );
		}

		$this->renderTemplate('admin/oauth-page');
	}

	public function add_ob_start() {
			ob_start();
	}

	public function get_auth_creds() {
		return array(
		  		'api_key' => CampaignMonitorPluginInstance()->get_option( 'api_key' ),
		);
	}

	public function get_list( $list_id ) {
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );

		return $list->get();
	}

	public function get_list_stats( $list_id ) {
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );

		return $list->get_stats();
	}

	public function get_list_fields( $list_id ) {
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );

		return $list->get_custom_fields();
	}

	public function get_clients() {
		$clients = new CS_REST_Clients('', $this->get_auth_creds() );

		return $clients->get()->response;
	}

	public function get_client_lists( $client_id ) {
		$clients = new CS_REST_Clients( $client_id, $this->get_auth_creds() );

		return $clients->get_lists()->response;
	}

	public function save_list( $client_id, $list_data ) {
		$list = new CS_REST_Lists( '', $this->get_auth_creds() );
		return $list->create( $client_id, $list_data );
	}

	public function add_list_fields( $list_id, $field ) {
        $field = str_replace( array( "\\'" ), array("'"), $field);
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );
        
		if ( isset( $field['Options'] ) ) {
			$input = array();
			if ( ! is_array( $field['Options'] ) ) {
				foreach ( explode( "\r\n", $field['Options'] ) as $o ) {
					$trimmed_o = trim( $o );
                    if ( ! empty( $trimmed_o ) ) {
						$input[] = sanitize_text_field( $o );
					}
				}

				$field['Options'] = $input;
			}
		}
		return $list->create_custom_field( $field );
	}

	public function update_list( $list_id, $list_data ) {
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );
		return $list->update( $list_data );
	}

	public function update_list_fields( $list_id, $field_key, $field ) {
        $field = str_replace( array( "\\'" ), array("'"), $field);
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );
		$list->update_custom_field( $field_key, $field );
		if ( isset( $field['Options'] ) ) {
			$input = array();
			if ( ! is_array( $field['Options'] ) ) {
				foreach ( explode( "\r\n", $field['Options'] ) as $o ) {
					$trimmed_o = trim( $o );
                    if ( ! empty( $trimmed_o ) ) {
						$input[] = sanitize_text_field( $o );
					}
				}
			} else {
				$input = $field['Options'];
			}
			$list->update_field_options( $field_key, $input, false );
		}
	}

	public function delete_list_fields( $list_id, $field_key ) {
		$list = new CS_REST_Lists( $list_id, $this->get_auth_creds() );
		$list->delete_custom_field( $field_key );
	}

	public function subscribe( $list_id, $subscriber ) {
		$subscription = new CS_REST_Subscribers( $list_id, $this->get_auth_creds() );
		return $subscription->add( $subscriber );
	}

	public function enabled() {

		if ( $this->enabled ) {
			$response = $this->get_clients();

			if ( is_array( $response ) ) {
				return true;
			}
		}

		return false;
	}

    public function get_company_name() {
	  $user = new CS_REST_General( $this->get_auth_creds() );
	  $client = $user->get_clients();
	  if ( 1 < count( $client->response ) ){
	  	return ' An Agency';
	  } else {
		return $client->response[0]->Name;
	  }
	}
}
