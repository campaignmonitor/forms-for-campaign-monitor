<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'wp_tables/campaign_monitor_wp_table_forms.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_form.php';

class CampaignMonitorAdminWizard extends CampaignMonitorBase {

	function __construct() {
        
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
//		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function add_admin_menu() {
        
        remove_submenu_page( 'campaign-monitor-options', 'campaign-monitor-options' );
        remove_menu_page( 'campaign-monitor-options' );
        
        add_menu_page(
			'Campaign Monitor',
			'Campaign <br/> Monitor',
			'manage_options',
			'campaign-monitor-forms',
			array( $this, 'wizard_page' ),
            plugin_dir_url( __FILE__ ) . '../img/cm-adm-icon.svg'
		);

		add_submenu_page(
			'campaign-monitor-forms',
			'Campaign Monitor Wizard',
			__('Forms', 'campaign-monitor'),
			'manage_options',
			'campaign-monitor-forms',
			array( $this, 'wizard_page' )
		);
        //add_action( "load-$hook", array( $this, 'add_options' ) );
        
        add_submenu_page(
			'',
			'Campaign Monitor Wizard',
			__('Add Form', 'campaign-monitor'),
			'manage_options',
			'campaign-monitor-add-wizard',
			array( $this, 'wizard_form_page' )
		);

		add_submenu_page(
			'',
			'Campaign Monitor Wizard',
			__('Edit Form', 'campaign-monitor'),
			'manage_options',
			'campaign-monitor-edit-wizard',
			array( $this, 'edit_wizard_page' )
		);
	}
    
    function custom_menu_page_removing() {
        remove_menu_page( 'campaign-monitor-options' );
    }
    
    function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'Forms',
			'default' => 50,
			'option' => 'forms_per_page'
		);
		add_screen_option( $option, $args );
	}
    
    public function wizard_page() {
		$this->renderTemplate('admin/forms-wp_table');
	}
    
    public function add_list( $title, $id ) {
		global $wpdb;
		if ( $id != null && ! $this->exists( $id ) ) {
			$wpdb->insert($wpdb->prefix.'cm_lists', array(
				'id'   => $id,
				'name' => $title
			));

			return true;
		}

		return false;
	}
    
    public function prepare_data( $data ) {
		$list_data = array();
		if ( array_key_exists( 'ConfirmedOptIn', $data ) ) {
			$list_data['ConfirmedOptIn'] = 1==$data['ConfirmedOptIn'];
            $list_data['ConfirmationSuccessPage'] = sanitize_text_field( $data['ConfirmationSuccessPage'] );
		}
		$list_data['title'] = sanitize_text_field( $data['title'] );
		return $list_data;
	}
    
    public function exists( $id ) {
		global $wpdb;

		$sql = "SELECT * FROM " . $wpdb->prefix . $this->tableName . " WHERE id = '" . intval( $id ) ."'";
		$list = $wpdb->get_results($sql);

		return (count($list) > 0)?true:false;
	}
    
    public function wizard_form_page() {
		$form = new CampaignMonitorForm();

        // ***CHECK IF NEEDED
        $formNumber = "0";
		$form->load($formNumber);

		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-add-wizard' ) ) {
                
                if ( isset( $_POST['title'] ) && ! empty( $_POST['title'] ) ) {

                    $response = CampaignMonitorPluginInstance()->connection->save_list( sanitize_text_field( $_POST['client'] ), $this->prepare_data( $_POST ) );

                    if ( 201 != $response->http_status_code ) {

                        CampaignMonitorPluginInstance()->connection->last_error = $response->response;
                    } else {

                        $response = CampaignMonitorPluginInstance()->connection->get_list( $response->response );

						$form->data['list_id'] = sanitize_text_field( $response->response->ListID );
                    }

                }else{
                    $form->data['list_id'] = sanitize_text_field( $_POST['list_id'] );
                }

				$form_fields = array();
                foreach( $_POST['fields'] as $k => $field ) {
                    $fieldName = array_key_exists( 'FieldName', $field ) ? stripslashes($field['FieldName']) : '';
					$field['FieldName'] = $fieldName;
					if ( is_numeric( $k ) ) {
						$new_field = CampaignMonitorPluginInstance()->connection->add_list_fields( $form->data['list_id'], $field );
						$key = str_replace( array( "\\'" ), array( "" ), $new_field->response);
						$cleanKey = str_replace(array( '[', ']' ), '', $key);

						$form_fields[$cleanKey] = $field;
                    
					} else {
						CampaignMonitorPluginInstance()->connection->update_list_fields( $form->data['list_id'], "[$k]", $field );
                        
                        if ($k == "email" || $k == "userInformation"){
                 		 $form_fields[$k] = $field;
                        }else{
                 		 $review_key = preg_replace('/\s+/', '', $field['FieldName']);
                 		 $form_fields[$review_key] = $field;
                        }
                        
					}
				}
                
                $form->enabled = intval( $_POST['enabled'] );
                $form->global = intval( $_POST['isGlobal'] );
                $form->type = sanitize_text_field( $_POST['type'] );
				$form->name = sanitize_text_field( $_POST['form_name'] );
                $form->data['form_title'] = stripslashes( sanitize_text_field( $_POST['form_title'] ) );
                $form->data['form_summary'] = stripslashes( sanitize_text_field( $_POST['form_summary'] ) );
                $form->data['submitText'] = stripslashes( sanitize_text_field( $_POST['submitText'] ) );
                $form->data['success_message'] = htmlspecialchars(sanitize_text_field( $_POST['success_message'] ));
                $form->data['success_message_title'] = htmlspecialchars(sanitize_text_field( $_POST['success_message_title'] ));
                $form->data['hasBadge'] = sanitize_text_field( $_POST['hasBadge'] );
				$form->data['fields'] = $form_fields;
                
                // Makes pages_list an array
                $pages_list = array();
                if ( ! is_array( $_POST['pages_list'] ) ) {
                    foreach ( explode( "\r\n", $_POST['pages_list'] ) as $o ) {
                        $trimmed_o = trim( $o );
                        if ( ! empty( $trimmed_o ) ) {
                            $pages_list[] = sanitize_text_field( $o );
                        }
                    }
                }
                
                switch ( $form->type ) {
                    case 'lightbox':
                    $form->data['lightbox_delay'] = sanitize_text_field( $_POST['lightbox_delay'] );
                    $form->data['lightbox_delay_seconds'] = intval( $_POST['lightbox_delay_seconds'] );
                    $form->data['lightbox_delay_height'] = sanitize_text_field( $_POST['lightbox_delay_height'] );
                    $form->data['show_in'] = $pages_list;
                    break;
                    case 'slider':
                        $form->data['slider_position'] = sanitize_text_field( $_POST['slider_position'] );
                        $form->data['show_in'] = $pages_list;
                    break;
                    case 'bar':
                       $form->data['bar_position'] = sanitize_text_field( $_POST['bar_position'] );
                       $form->data['show_in'] = $pages_list;
                    break;
                    case 'button':
                        $form->data['text'] = sanitize_text_field( $_POST['button_text'] );
                    break;  
                }
                
                if ( isset( $_POST['toDelete'] ) ) {
					foreach( $_POST['toDelete'] as $d ) {
						CampaignMonitorPluginInstance()->connection->delete_list_fields( sanitize_text_field( $_POST['list_id'] ), $d );
					}
				}

				$form->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );

			} else {
				wp_nonce_ays( 'campaign-monitor-add-wizard' );
			}
		}
		$this->renderTemplate('admin/wizard-form');
	}
    
    public function edit_wizard_page() {
		$form = new CampaignMonitorForm();
		if ( ( ! isset( $_GET['form'] ) && empty( $_GET['form'] ) ) ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );
		}
		$form->load($_GET['form']);
		if ( 0 == $form->id ) {
			wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );
		}
		if ( isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'campaign-monitor-edit-wizard' ) ) {
                
                
                
                $form_fields = array();
                foreach( $_POST['fields'] as $k => $field ) {
					if ( ! isset( $field['FieldName'] ) ) {
						$field['FieldName'] = $k;
					} else {
						$field['FieldName'] = stripslashes($field['FieldName']);
					}
					if ( is_numeric( $k ) ) {
						$new_field = CampaignMonitorPluginInstance()->connection->add_list_fields( sanitize_text_field( $_POST['list_id'] ), $field );
						$key = str_replace( array( "\\'" ), array( "" ), $new_field->response);
						$cleanKey = str_replace(array( '[', ']' ), '', $key);

						$form_fields[$cleanKey] = $field;
                    
					} else {
						CampaignMonitorPluginInstance()->connection->update_list_fields( sanitize_text_field( $_POST['list_id'] ), "[$k]", $field );
                        
                        if ($k == "email" || $k == "userInformation"){
                 		 $form_fields[$k] = $field;
                        }else{
                 		 $review_key = preg_replace('/\s+/', '', $field['FieldName']);
                 		 $form_fields[$review_key] = $field;
                        }
					}
				}
                
                
                
                $form->enabled = intval( $_POST['enabled'] );
                $form->global = intval( $_POST['isGlobal'] );
//				$form->name = $_POST['title'];
                $form->data['form_title'] = sanitize_text_field( $_POST['form_title'] );
                $form->data['form_summary'] = sanitize_text_field( $_POST['form_summary'] );
                $form->data['submitText'] = sanitize_text_field( $_POST['submitText'] );
                $form->data['success_message'] = htmlspecialchars(sanitize_text_field( $_POST['success_message'] ));
                $form->data['success_message_title'] = htmlspecialchars(sanitize_text_field( $_POST['success_message_title'] ));
                $form->data['hasBadge'] = sanitize_text_field( $_POST['hasBadge'] );
                $form->data['fields'] = $form_fields;
                
                // Makes pages_list an array
                $pages_list = array();
                if ( ! is_array( $_POST['pages_list'] ) ) {
                    foreach ( explode( "\r\n", $_POST['pages_list'] ) as $o ) {
                        $trimmed_o = trim( $o );
                        if ( ! empty( $trimmed_o ) ) {
                            $pages_list[] = sanitize_text_field( $o );
                        }
                    }
                }
                
                
                switch ( $form->type ) {
                    case 'lightbox':
                    $form->data['lightbox_delay'] = sanitize_text_field( $_POST['lightbox_delay'] );
                    $form->data['lightbox_delay_seconds'] = intval( $_POST['lightbox_delay_seconds'] );
                    $form->data['lightbox_delay_height'] = sanitize_text_field( $_POST['lightbox_delay_height'] );
                    $form->data['show_in'] = $pages_list;
                    break;
                    case 'slider':
                        $form->data['slider_position'] = sanitize_text_field( $_POST['slider_position'] );
                        $form->data['show_in'] = $pages_list;
                    break;
                    case 'bar':
                        $form->data['bar_position'] = sanitize_text_field( $_POST['bar_position'] );
                        $form->data['show_in'] = $pages_list;
                    break;
                    case 'button':
                        $form->data['text'] = sanitize_text_field( $_POST['button_text'] );
                    break;   
                }
                
                if ( isset( $_POST['toDelete'] ) ) {
					foreach( $_POST['toDelete'] as $d ) {
						CampaignMonitorPluginInstance()->connection->delete_list_fields( sanitize_text_field($_POST['list_id']), $d );
					}
				}
                
				$form->save();
				wp_redirect( admin_url("admin.php?page=campaign-monitor-forms") );

			} else {
				wp_nonce_ays( 'campaign-monitor-edit-wizard' );
			}
		}
		$this->renderTemplate('admin/wizard-edit');
	}

}