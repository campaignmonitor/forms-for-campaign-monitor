<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CampaignMonitorWpTableForms extends WP_List_Table {

	/**
	 * @var string
	 */
	private static $table_name = 'cm_elements';

	/**
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Form',  'campaign-monitor' ), //singular name of the listed records
			'plural'   => __( 'Forms', 'campaign-monitor' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		) );

	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'name'     => __( 'Name', 'campaign-monitor'),
            'list'     => __( 'List', 'campaign-monitor'),
            'type'     => __( 'Type', 'campaign-monitor'),
            'status'   => __( 'Status', 'campaign-monitor'),
            'location' => __( 'Location', 'campaign-monitor'),
		);
		return $columns;
	}

	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array('name',false),
            'list' => array('information',true),
            'type' => array('type',false),
            'status' => array('status',false),
            'location' => array('location',false),
		);
		return $sortable_columns;
	}

	/**
	 *
	 */
	function prepare_items() {

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'lists_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );


		$this->items = self::get_data( $per_page, $current_page );
	}

	function column_name( $item ) {

		// create a nonce
		$delete_nonce = wp_create_nonce( 'cm_delete' );
        $disable_nonce = wp_create_nonce( 'cm_disable' );
        $enable_nonce = wp_create_nonce( 'cm_enable' );

		$title = '<strong>' . sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&form=%s'), 'campaign-monitor-edit-wizard','edit',$item['id'] ) , stripcslashes($item['name']) ) . '</strong>';
        	
        if ($item['enabled'] == 1){
            $enabled_option = '<a href="?page='.$_REQUEST['page'].'&action=disable&form='.$item['id'].'&_wpnonce='.$disable_nonce.'">Disable</a>';
        }else if ($item['enabled'] == 0){
            $enabled_option = '<a href="?page='.$_REQUEST['page'].'&action=enable&form='.$item['id'].'&_wpnonce='.$enable_nonce.'">Enable</a>';
        } else {
        	$enabled_option = '';
        }

        $actions = array(
            'edit'      => sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&form=%s'), 'campaign-monitor-edit-wizard','edit',$item['id'] ) , __('Edit', 'campaign-monitor') ),
            'delete'    => sprintf('<a href="?page=%s&action=%s&form=%s&_wpnonce=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id'],$delete_nonce),
            'enabled'    => $enabled_option
        );

        return $title . $this->row_actions( $actions );
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
            case 'list':
                $info_array = maybe_unserialize($item['information']);
                $list_id = $info_array['list_id'];
            
                $list = CampaignMonitorPluginInstance()->connection->get_list( $list_id )->response;
            	if (property_exists($list, 'Title')) {
                	return stripslashes($list->Title);
                }
                return '<i>deleted list</i>';
            	break;
            case 'type':
                switch( $item['type'] ) {
                    case 'lightbox':
                        return "Lightbox";
                    case 'slider':
                        return "Slide-Out";
                    case 'bar':
                        return "Bar";
                    case 'button':
                        return "Button";
                    case 'simple_form':
                        return "Embedded";
                }
            	break;
            case 'status':
                switch( $item['enabled'] ) {
                    case '0':
                        return "Disabled";
                    case '1':
                        return "Enabled";
                    case '2':
                        return "Disconnected";
                }
            	break;
            case 'location':
                switch( $item['global'] ) {
                    case '0':
                        $edit_targeted_link = admin_url('admin.php?page=campaign-monitor-edit-wizard&action=edit&form='.$item['id'].'#step-3-options');
                        return '<a href="'.$edit_targeted_link.'">Targeted</a>';
                    case '1':
                        return "Global";
                    case '2':
                        return "Embedded";
                }
            	break;
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return array
	 */
	public static function get_data( $per_page = 50, $page_number = 1 ) {

		global $wpdb;
		$table = self::$table_name;

		$sql = "SELECT * FROM {$wpdb->prefix}{$table}";


		if ( isset( $_REQUEST['forms_enabled'] ) && $_REQUEST['forms_enabled'] != '-1' ) {
			$sql .= ' WHERE enabled = ' . intval($_REQUEST['forms_enabled']);
		}

		//default filter is Enabled, not all.
		if ( !isset( $_REQUEST['forms_enabled'] ) ) {
			$sql .= ' WHERE enabled = 1';
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Delete a list record.
	 *
	 * @param int $id list id
	 */
	public static function delete( $id ) {
		global $wpdb;
		$table = self::$table_name;
		$wpdb->delete(
			"{$wpdb->prefix}{$table}",
			array( 'id' => $id ),
			array( '%d' )
		);
	}
    
    public static function disable( $id ) {
		global $wpdb;
		$table = self::$table_name;
		$wpdb->update(
            "{$wpdb->prefix}{$table}",
			array( 'enabled' => 0 ),
			array( 'id' => $id )
		);
	}
    
    public static function enable( $id ) {
		global $wpdb;
		$table = self::$table_name;
		$wpdb->update(
            "{$wpdb->prefix}{$table}",
			array( 'enabled' => 1 ),
			array( 'id' => $id )
		);
	}

	public static function record_count() {
		global $wpdb;
		$table = self::$table_name;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}{$table}";

		return $wpdb->get_var( $sql );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'cm_delete' ) ) {
				die( "Hey! You don't have permission to do that! ");
			}
			else {
				self::delete( absint( $_GET['form'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete( $id );

			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
        
        
        if ( 'disable' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'cm_disable' ) ) {
				die( "Hey! You don't have permission to do that! ");
			}
			else {
				self::disable( absint( $_GET['form'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		// If the disable bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-disable' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-disable' )
		) {

			$disable_ids = esc_sql( $_POST['bulk-disable'] );

			// loop over the array of record IDs and disable them
			foreach ( $disable_ids as $id ) {
				self::disable( $id );

			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
        
        if ( 'enable' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'cm_enable' ) ) {
				die( "Hey! You don't have permission to do that! ");
			}
			else {
				self::enable( absint( $_GET['form'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		// If the disable bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-enable' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-enable' )
		) {

			$disable_ids = esc_sql( $_POST['bulk-enable'] );

			// loop over the array of record IDs and disable them
			foreach ( $enable_ids as $id ) {
				self::enable( $id );

			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}

	function get_views(){
		$views = array();
		$current = ( isset($_REQUEST['forms_enabled']) ? $_REQUEST['forms_enabled'] : '1');

		//All link
		$class = ($current == '-1' ? ' class="current"' :'');
		$all_url = add_query_arg('forms_enabled', '-1');
		$views['all'] = "<a href='{$all_url }' {$class} >All</a>";

		//Foo link
		$foo_url = remove_query_arg('forms_enabled');
		$class = ($current == '1' ? ' class="current"' :'');
		$views['enabled'] = "<a href='{$foo_url}' {$class} >Enabled</a>";

		//Bar link
		$bar_url = add_query_arg('forms_enabled','0');
		$class = ($current == '0' ? ' class="current"' :'');
		$views['isDisabled'] = "<a href='{$bar_url}' {$class} >Disabled</a>";
        
        //Disconnected link
		$bar_url = add_query_arg('forms_enabled','2');
		$class = ($current == '2' ? ' class="current"' :'');
		$views['isDisconnected'] = "<a href='{$bar_url}' {$class} >Disconnected</a>";

		return $views;
	}
}