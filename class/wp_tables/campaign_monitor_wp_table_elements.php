<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CampaignMonitorWpTableElements extends WP_List_Table {

	/**
	 * @var string
	 */
	private static $table_name = 'cm_elements';

	/**
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Element',  'campaign-monitor' ), //singular name of the listed records
			'plural'   => __( 'Elements', 'campaign-monitor' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		) );

	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'name'    => __( 'Name', 'campaign-monitor'),
			'type'    => __( 'Type', 'campaign-monitor'),
			'enabled' => __( 'Enabled', 'campaign-monitor'),
			'global'  => __( 'Global / Targeted' , 'campaign-monitor')
		);
		return $columns;
	}

	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array('name',false),
			'type' => array('type',false),
			'enabled' => array('enabled',false),
			'global' => array('global',false),
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

		$per_page     = $this->get_items_per_page( 'elements_per_page', 25 );
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

		$title = '<strong>' . sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&e=%s'), 'campaign-monitor-edit-element','edit',$item['id'] ) , $item['name'] ) . '</strong>';

		$actions = array(
			'edit'      => sprintf('<a href="%s">%s</a>', sprintf( admin_url('admin.php?page=%s&action=%s&e=%s'), 'campaign-monitor-edit-element','edit',$item['id'] ) , __('Edit', 'campaign-monitor') ),
			'delete'    => sprintf('<a href="?page=%s&action=%s&e=%s&_wpnonce=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id'],$delete_nonce),
		);

		return $title . $this->row_actions( $actions );
	}

	function column_global( $item ) {
		switch( $item['type'] ){
			case 'button':
			case 'simple_form':
				return "[cm_{$item['type']} id={$item['id']}]";
			default:
				$data = maybe_unserialize( $item['information'] );
				if ( 1 == $item['global'] ) {
					return __( 'Global', 'campaign-monitor' );
				} elseif ( array_key_exists('show_in', $data ) && !empty( $data['show_in'] ) ) {
					return __( 'Targeted', 'campaign-monitor' );
				}
		}
	}

		function column_enabled( $item ) {
		switch( $item['type'] ){
			case 'button':
			case 'simple_form':
				return __( 'Use shortcode to enable', 'campaign-monitor' );
			default:
				return ($item[ 'enabled' ] == 1)?__('enabled','campaign-monitor'):__('disabled','campaign-monitor');
		}
	}


	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'id':
				return $item[ $column_name ];
			case 'name':
				return $item[ $column_name ];
			case 'type':
				return CampaignMonitorPluginInstance()->humanize( $item[ $column_name ] );
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
				self::delete( absint( $_GET['e'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}
	}

}