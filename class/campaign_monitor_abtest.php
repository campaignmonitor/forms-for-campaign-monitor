<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_base.php' );

class CampaignMonitorABTest extends CampaignMonitorBase {
	protected $tableName = 'cm_abtests';
	public $data;
	public $id;
	public $name;
	public $global;
	public $enabled;
	public $created;
	public $updated;
	public $render = 0;
	public $defaults = array(
			'name' => 'New A/B Test',
			'global' => 0,
			'enabled' => 0,
			'data' => array(
				'global_show'   => 0,
				'first_element' => 0,
				'first_element_shows' =>0,
				'first_element_actions' => 0,
				'first_element_submissions' => 0,
				'second_element' => 0,
				'second_element_shows' =>0,
				'second_element_actions' => 0,
				'second_element_submissions' => 0,
				'show_in' => array(),
			),
	);
	public $element;
	public $to_be_rendered = false;

	public function save( ) {
		if ( $this->id != 0 && $this->exists( $this->id ) ) {
			return $this->update();
		} else {
			return $this->create();
		}
	}

	public function exists( $id ) {
		$element = new self();
		$element->load( $id );

		return $element->id > 0;
	}

	private function update() {
		global $wpdb;

		$this->sanitize();

		$wpdb->update(
			$wpdb->prefix.$this->tableName,
			array(
				'name' => sanitize_text_field($this->name),
			    'information' => maybe_serialize( $this->data ),
				'global' => intval($this->global),
				'enabled' => intval($this->enabled),
				'updated' => current_time( 'mysql' ),
			),
			array( 'id'   => $this->id )
		);

		return true;
	}

	private function create() {
		global $wpdb;

		$this->sanitize();

		$wpdb->insert($wpdb->prefix.$this->tableName, array(
			'name' => sanitize_text_field($this->name),
			'global' => intval($this->global),
			'enabled' => intval($this->enabled),
			'information' => maybe_serialize( $this->data ),
			'updated' => current_time( 'mysql' ),
		));

		$this->id = $wpdb->insert_id;
		return true;
	}

	public function sanitize() {

		$this->name = sanitize_text_field( $this->name );
		$this->global = intval( $this->global );
		$this->enabled = intval( $this->enabled );
		$this->data = $this->sanitize_array( $this->data );

	}

	public function load( $id = null ) {
		if ( ! is_null( $id ) ) {
			$this->id = intval($id);
		}
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE id = " . $this->id;

		$result = $wpdb->get_results($sql);

		if( count($result) > 0 )
		{
			$result = $result[0];
			$this->data = array_merge( $this->defaults['data'], maybe_unserialize($result->information) );
			$this->name = $result->name;
			$this->global = $result->global;
			$this->enabled = $result->enabled;
			$this->created = $result->created;
			$this->updated = $result->updated;
		} else {
			$this->id = 0;
		}

	}

	public function load_all() {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE enabled=1";
		$result = $wpdb->get_results($sql);

		$abtests = array();
		if( count($result) > 0 )
		{
			foreach( $result as $r ) {
				$ab = new CampaignMonitorABTest();
				$ab->load( $r->id );
				$abtests[] = $ab;
			}
		}

		return $abtests;
	}

	public function render() {

		if ( 0 == $this->id || !$this->to_be_rendered ) {

			return '';
		}

		$this->renderTemplate( 'abtest-render');
	}

	public function chooseRender() {
		if ( 1 == mt_rand(1,2) ) {
			$this->render = 'first_element';
		} else {
			$this->render = 'second_element';
		}
		$this->to_be_rendered = true;
		$this->data[$this->render.'_shows'] += 1;
		$this->save();
	}

	public function contains ( $k, $j ) {
		if (
			 1 == $this->enabled &&
		     $k->to_be_rendered &&
		     $j->to_be_rendered &&
		     in_array( $k->id, array( $this->data['first_element'], $this->data['second_element'] )) &&
		     in_array( $j->id, array( $this->data['first_element'], $this->data['second_element'] ))
			) {
			$this->chooseRender();
			if ( $this->data[$this->render] == $k->id ) {
				$this->element = 'k';
			} else {
				$this->element = 'j';
			}

			return true;
		}

		return false;
	}

	public function with_slug ( $slug ) {
		if ( ! empty( $this->data['show_in'] ) ) {
			if ( ! in_array( $slug, $this->data['show_in'] ) ) {
				$this->enabled = 0;
			}
		}
	}

}

