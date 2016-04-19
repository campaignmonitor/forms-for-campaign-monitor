<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_element.php' );

class CampaignMonitorSimpleform extends CampaignMonitorElement {
	public $type = "simple_form";

	public $defaults = array(
			'name' => 'New Simple Form',
			'type' => 'simple_form',
			'data' => array(
				'form_id' => '',
				'additional_css' => ''
			),
	);

	function __construct( ) {
		$this->name = $this->defaults['name'];
		$this->data = $this->defaults['data'];
		$this->id = 0;
	}

	function render() {
        ob_start();
		$this->renderTemplate('simple_form-render');
        return ob_get_clean();

	}

}