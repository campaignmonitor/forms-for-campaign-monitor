<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CampaignMonitorWpTableLists extends WP_List_Table {

	/**
	 * @var string
	 */
	private static $table_name = 'cm_lists';

	/**
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'List',  'campaign-monitor' ), //singular name of the listed records
			'plural'   => __( 'Lists', 'campaign-monitor' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		) );

	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'name'        => __( 'Name', 'campaign-monitor'),
			'subscribers' => __( 'Subscribers', 'campaign-monitor' ),
			'registration'=> __( 'In Registration', 'campaign-monitor'),
			'comments'    => __( 'In Comments', 'campaign-monitor'),
			'imported'    => __( 'Imported', 'campaign-monitor')
		);
		return $columns;
	}

	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array('name',false),
			'imported' => array('imported',false),
			'registration' => array('registration',false),
			'comments' => array('comments',false),
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

		$per_page     = $this->get_items_per_page( 'lists_per_page', 10 );
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

		$title = '<strong>' . sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&list=%s'), 'campaign-monitor-edit-list-fields','fields',$item['id'] ) , $item['name'] ) . '</strong>';

		$actions = array(
			'fields'    => sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&list=%s'), 'campaign-monitor-edit-list-fields','fields',$item['id'] ) , __('Edit Custom Fields', 'campaign-monitor') ),
			'edit'      => sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&list=%s'), 'campaign-monitor-edit-list','edit',$item['id'] ) , __('Edit', 'campaign-monitor') ),
			'delete'    => sprintf('<a href="?page=%s&action=%s&list=%s&_wpnonce=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id'],$delete_nonce),
		);

		return $title . $this->row_actions( $actions );
	}

	function column_subscribers ( $item ) {
		$list = CampaignMonitorPluginInstance()->connection->get_list_stats( $item['id'] );

		return $list->response->TotalActiveSubscribers;
	}
	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'imported':
				return $item[ $column_name ];
			case 'registration':
			case 'comments':
				return ( $item[ $column_name ] == 1 ) ? __('Yes', 'campaign-monitor') : __('No', 'campaign-monitor');
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
	public static function get_data( $per_page = 5, $page_number = 1 ) {

		global $wpdb;
		$table = self::$table_name;

		$sql = "SELECT * FROM {$wpdb->prefix}{$table}";

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
				self::delete( absint( $_GET['list'] ) );

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
	}
}