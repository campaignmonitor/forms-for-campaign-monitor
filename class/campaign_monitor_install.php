<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CampaignMonitorInstall {

	function __construct() {

	}

	function install() {
		$this->lists_table();
		$this->forms_table();
		$this->elements_table();
		$this->abtests_table();
	}

	function lists_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;

		$table_name = $wpdb->prefix . "cm_lists";

		$charset_collate = $wpdb->get_charset_collate();

		//Lists Creation Query
		$sql = "CREATE TABLE $table_name (
				  id varchar(64) NOT NULL,
				  name varchar(255) DEFAULT '' NOT NULL,
				  comments int DEFAULT 0 NOT NULL,
				  comments_text VARCHAR(255) DEFAULT '' NOT NULL,
				  registration int DEFAULT 0 NOT NULL,
				  registration_text VARCHAR(255) DEFAULT '' NOT NULL,
				  imported timestamp DEFAULT CURRENT_TIMESTAMP,
				  UNIQUE KEY id (id)
				) $charset_collate;";

		dbDelta( $sql );

	}

	function forms_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;

		$table_name = $wpdb->prefix . "cm_forms";

		$charset_collate = $wpdb->get_charset_collate();

		//Forms Creation Query
		$sql = "CREATE TABLE $table_name (
				  id INT NOT NULL AUTO_INCREMENT,
				  name varchar(255) DEFAULT '' NOT NULL,
				  information TEXT,
				  created timestamp DEFAULT CURRENT_TIMESTAMP,
				  updated timestamp NULL,
				  UNIQUE KEY id (id)
				) $charset_collate;";
		dbDelta( $sql );

	}

	function elements_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;

		$table_name = $wpdb->prefix . "cm_elements";

		$charset_collate = $wpdb->get_charset_collate();

		//Forms Creation Query
		$sql = "CREATE TABLE $table_name (
				  id INT NOT NULL AUTO_INCREMENT,
				  enabled SMALLINT DEFAULT 0,
				  global SMALLINT DEFAULT 0,
				  type varchar(255) DEFAULT '' NOT NULL,
				  name varchar(255) DEFAULT '' NOT NULL,
				  information TEXT,
				  created timestamp DEFAULT CURRENT_TIMESTAMP,
				  updated timestamp NULL,
				  UNIQUE KEY id (id)
				) $charset_collate;";
		dbDelta( $sql );

	}

	function abtests_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;

		$table_name = $wpdb->prefix . "cm_abtests";

		$charset_collate = $wpdb->get_charset_collate();

		//Forms Creation Query
		$sql = "CREATE TABLE $table_name (
				  id INT NOT NULL AUTO_INCREMENT,
				  enabled SMALLINT DEFAULT 0,
				  global SMALLINT DEFAULT 0,
				  name varchar(255) DEFAULT '' NOT NULL,
				  information TEXT,
				  created timestamp DEFAULT CURRENT_TIMESTAMP,
				  updated timestamp NULL,
				  UNIQUE KEY id (id)
				) $charset_collate;";
		dbDelta( $sql );
	}



}