<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_base.php' );

class CampaignMonitorForm extends CampaignMonitorBase {
	private $tableName = 'cm_elements';
    private $_success_message_title = 'Thank you!';
    private $_success_message = 'Your subscription has been confirmed. <br> You\'ll hear from us soon.';
	public $data;
	public $id;
	public $name;
	public $pre_info;
	public $element_id =0;
	public $abtest_id = 0;
	public $previewing = false;


    /**
     * @return string
     */
    public function getSuccessMessageTitle()
    {
        $title = (isset($this->data['success_message_title'])) ? $this->data['success_message_title'] : $this->_success_message_title;
        return  $title;
    }

    /**
     * @param string $sent_message_title
     */
    public function setSuccessMessageTitle( $sent_message_title )
    {
        $this->_success_message_title = $sent_message_title;
        $this->data['success_message_title'] = $sent_message_title;
    }

    /**
     * @return string
     */
    public function getSuccessMessage()
    {
        $message = (isset($this->data['success_message'])) ? $this->data['success_message'] : $this->_success_message;
        return  $message;
    }

    /**
     * @param string $sent_message
     */
    public function setSuccessMessage( $sent_message )
    {
        $this->_success_message = $sent_message;
        $this->data['success_message'] = $sent_message;
    }

	private function get_defaults() {
		return array(
			'enabled' => '1',
            'global' => '1',
            'type' => '',
            'name' => 'New Form',
			'data' => array(
				'submitText' => 'Submit Form',
				'list_id' => '',
                'success_message_title' => htmlspecialchars('Thank you!'),
                'success_message' => htmlspecialchars("Your subscription has been confirmed. You'll hear from us soon."),
				'fields'  => array(),
			),
		);
	}

	function __construct() {
		$defaults = $this->get_defaults();
		$this->enabled = $defaults['enabled'];
        $this->global = $defaults['global'];
        $this->type = $defaults['type'];
        $this->name = $defaults['name'];
		$this->data = $defaults['data'];
		$this->id = 0;
	}

	public function save( ) {
		//if ( CampaignMonitorPluginInstance()->admin->lists->exists( $this->data['list_id'] ) ) {
			if ( $this->id != 0 && $this->exists( $this->id ) ) {
				return $this->update();
			} else {
				return $this->create();
			}
		//}

		//return false;
	}

	public function exists( $id ) {
		$form = new self();
		$form->load( $id );

		return $form->id > 0;
	}

	private function update() {
		global $wpdb;

		$this->sanitize();

		$wpdb->update(
			$wpdb->prefix.$this->tableName,
			array(
				'enabled'     => $this->enabled,
				'global'      => $this->global,
				'type'        => $this->type,
				'name'        => $this->name,
				'information' => maybe_serialize( $this->data ),
				'updated'     => current_time( 'mysql' )
			),
			array( 'id'   => $this->id )
		);

		return true;
	}

	private function create() {
		global $wpdb;

		$this->sanitize();

		$wpdb->insert($wpdb->prefix.$this->tableName, array(
			'enabled' => $this->enabled,
            'global' => $this->global,
            'type' => $this->type,
            'name' => $this->name,
			'information' => maybe_serialize( $this->data ),
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
			$this->data = maybe_unserialize($result->information);
            $this->enabled = $result->enabled;
            $this->global = $result->global;
            $this->type = $result->type;
			$this->name = $result->name;
		} else {
			$this->id = 0;
		}

	}

	public function get_field_data( $field, $key ) {
		if ( array_key_exists( 'fields', $this->data ) && array_key_exists( $field, $this->data['fields'] ) && array_key_exists( $key, $this->data['fields'][$field] ) ) {

			return $this->data['fields'][$field][$key];
		}

		return '';
	}

	public function clean_key( $key, $full=false ) {
		if ($full) {
		  return str_replace(array("[", "]", "\\'", "'", "\\"), array("", "", "", "", ""),
			$key);
		}
	  return str_replace(array("[", "]", "\\'"), array("", "", ""), $key);
	}

	public function render() {
		if ( 0 == $this->id ) {

			return '';
		}

		ob_start();
		if ( $this->previewing ) {
			$this->renderTemplate('forms-preview-render');
		} else {
			$this->renderTemplate('forms-render');
		}

		return ob_get_clean();
	}

	public function submit() {

	}

	public function get_all() {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName;

		return $wpdb->get_results($sql);
	}
    
    public function get_next_id() {
		global $wpdb;
		$result = $wpdb->get_results("SHOW TABLE STATUS");
        foreach($result as $table) {
            if ($table->Name == $wpdb->prefix.$this->tableName){
                $increment = $table->Auto_increment;
            }
        }
        return $increment;
	}

	public function single_line() {
		$count = 1;
		foreach( $this->data['fields'] as $field ) {
			if ( isset( $field['enabled'] ) && 1 == $field['enabled'] ) {
				$count++;
			}
		}

		if ( $count > 2 ) {
			return "multiline";
		}

		return "dingle_line";
	}

}