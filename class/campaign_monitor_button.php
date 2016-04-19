<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_element.php' );

class CampaignMonitorButton extends CampaignMonitorElement {
	public $type = "button";

	public $defaults = array(
			'name' => 'New Button',
			'global' => 0,
			'enabled' => 0,
			'type' => 'button',
			'data' => array(
				'form_id' => '',
				'text' => 'Click Me',
				'additional_css' => ''
			),
	);

	function __construct( ) {
		$this->name = $this->defaults['name'];
		$this->data = $this->defaults['data'];
		$this->id = 0;
	}

	public function render() {
		if ( 0 == $this->id ) {
			return '';
		}

		ob_start();
		$this->renderTemplate('button-render');
		add_action('wp_footer', array($this, 'renderForm'));
		return ob_get_clean();
	}

	public function renderForm(){
		$this->renderTemplate('button-form-render');
	}

}