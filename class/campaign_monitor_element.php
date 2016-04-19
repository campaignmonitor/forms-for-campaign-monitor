<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_base.php' );

class CampaignMonitorElement extends CampaignMonitorBase {
	protected $tableName = 'cm_elements';
	public $data;
	public $id;
	public $name;
	public $global;
	public $enabled;
	public $type;
	public $abtest_id=0;
	public $defaults = array(
			'name' => 'New Slider',
			'global' => 0,
			'enabled' => 0,
			'type' =>'',
			'data' => array(
				'form_id' => ''
			),
	);
	public $to_be_rendered = true;
	public $preview = false;

	public function save( ) {
		$form = new CampaignMonitorForm();
		if ( $form->exists( $this->data['form_id'] ) ) {
			if ( $this->id != 0 && $this->exists( $this->id ) ) {
				return $this->update();
			} else {
				return $this->create();
			}
		}

		return false;
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
				'name' => $this->name,
			    'information' => maybe_serialize( $this->data ),
				'global' => $this->global,
				'enabled' => $this->enabled,
			),
			array( 'id'   => $this->id )
		);

		return true;
	}

	private function create() {
		global $wpdb;

		$this->sanitize();

		$wpdb->insert($wpdb->prefix.$this->tableName, array(
			'name' => $this->name,
			'global' => $this->global,
			'enabled' => $this->enabled,
			'information' => maybe_serialize( $this->data ),
			'type' => $this->type
		));

		$this->id = $wpdb->insert_id;
		return true;
	}

	public function sanitize() {

		$this->name = sanitize_text_field( $this->name );
		$this->global = intval( $this->global );
		$this->enabled = intval( $this->enabled );
		$this->data = $this->sanitize_array( $this->data );
		$this->type = sanitize_text_field( $this->type );

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
			$this->data = array_merge( $this->defaults['data'], (array)maybe_unserialize($result->information) );
			$this->name = $result->name;
			$this->global = $result->global;
			$this->enabled = $result->enabled;
			$this->type = $result->type;
		} else {
			$this->id = 0;
		}

	}

	public function render() {

		if ( 0 == $this->id && $this->to_be_rendered ) {

			return '';
		}

		ob_start();
		$this->renderTemplate( $this->type.'-render');

		return ob_get_clean();
	}

	public function enableFromSlug( $slug ) {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE enabled=1 AND information LIKE '%\"" . $slug . "\"%'";

		$result = $wpdb->get_results($sql);
		$elements = array();
		if( count($result) > 0 )
		{
			foreach( $result as $r ) {
				$element = new CampaignMonitorElement();
				$element->load($r->id);
				$elements[] = $element;
			}
		}

		return $elements;
	}

	public function get_all() {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName;
		$result = $wpdb->get_results($sql);
		return $result;
	}

	public function load_global() {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE global = 1 and enabled = 1";

		$result = $wpdb->get_results($sql);

		$elements = array();
		foreach( $result as $e ) {
			$temp = new CampaignMonitorElement();
			$temp->load($e->id);
			$elements[] = $temp;
		}
		return $elements;
	}

	public function render2() {
		switch ( $this->type ) {
			case 'lightbox':
				$e = new CampaignMonitorLightbox();
				$e->load( $this->id );
				$e->render();
				break;
			case 'slider':
				$e = new CampaignMonitorSlider();
				$e->load( $this->id );
				echo $e->render();
				break;
			case 'bar':
				$e = new CampaignMonitorBar();
				$e->load( $this->id );
				echo $e->render();
				break;
		}
	}

	public function preparePreview() {
		$form = New CampaignMonitorForm();
		$form->id = 7357; //TEST => 7357
		if ( isset( $_POST['form_id'] ) ) {
			$form->load( intval( $_POST['form_id'] ) );
		}
		$form_fields = array();
		foreach( $_POST['fields'] as $k => $field ) {
			if ( isset($field['FieldName']) ) {
				$field['FieldName'] = stripslashes($field['FieldName']);
			} else {
				$field['FieldName'] = stripslashes($k);
			}
			if ( is_numeric( $k ) ) {

				$cleanKey = str_replace(array( '[', ']' ), '', $field['FieldName']);

				$form_fields[$cleanKey] = $field;

			} else {

				if ($k == "email" || $k == "userInformation"){
					$form_fields[$k] = $field;
				}else{
					$review_key = preg_replace('/\s+/', '', $field['FieldName']);
					$form_fields[$review_key] = $field;
				}
			}
		}


		$form->enabled = 1;
		$form->global = 1;
		$form->previewing = true;
		$form->data['form_title'] = $_POST['form_title'];
		$form->data['form_summary'] = $_POST['form_summary'];
		$form->data['submitText'] = $_POST['submitText'];
        if (isset($_POST['hasBadge'])){
            $form->data['hasBadge'] = $_POST['hasBadge'];
        }else{
            $form->data['hasBadge'] = 0;
        }
		$form->data['fields'] = $form_fields;
		if ( isset( $_POST['list_id'] ) ) {
			$form->data['list_id'] = $_POST['list_id'];
		}
		if ( isset( $_POST['preview_type'] ) ){
			$form->type = sanitize_text_field( $_POST['preview_type'] );
		}
		if ( isset( $_POST['type'] ) ){
			$form->type = sanitize_text_field( $_POST['type'] );
		}

		switch ( $form->type ) {
			case 'lightbox':
				if ( ! isset( $_POST['lightbox_delay'] ) ) {
					$form->data['lightbox_delay'] = "immediately";
				} else {
					$form->data['lightbox_delay'] = sanitize_text_field( $_POST['lightbox_delay'] );
					if ( isset( $_POST['lightbox_delay_seconds'] ) ){
						$form->data['lightbox_delay_seconds'] = sanitize_text_field( $_POST['lightbox_delay_seconds'] );
					}
					if ( isset( $_POST['lightbox_delay_height'] ) ){
						$form->data['lightbox_delay_height'] = sanitize_text_field( $_POST['lightbox_delay_height'] );
					}
				}
				break;
			case 'slider':
				if ( !isset( $_POST['slider_position'] ) ) {
					$form->data['slider_position'] = "top";
				} else {
					$form->data['slider_position'] = sanitize_text_field( $_POST['slider_position'] );
				}
				break;
			case 'bar':
				if ( !isset( $_POST['bar_position'] ) ) {
					$form->data['bar_position'] = "top";
				} else {
					$form->data['bar_position'] =sanitize_text_field( $_POST['bar_position'] );
				}
				break;
			case 'button':
				$form->data['text'] = sanitize_text_field( $_POST['button_text'] );
				break;
		}

		$this->data = $form->data;
		$this->type = $form->type;
		$this->previewForm = $form;
		$this->id = $form->id;
		$this->preview = true;
		$this->to_be_rendered = true;
	}

	public function renderPreview() {

		if ( 0 == $this->id && $this->to_be_rendered ) {

			return '';
		}

		if ( "button" == $this->type ) {
			$btn = new CampaignMonitorButton();
			$btn->data = $this->data;
			$btn->id = $this->id;
			$btn->previewForm = $this->previewForm;
			$btn->preview = true;
			add_action('wp_footer', array($btn, 'renderForm'));
		}

		ob_start();
		$this->renderTemplate( $this->type.'-render');

		return ob_get_clean();
	}
}