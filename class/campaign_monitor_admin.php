<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_settings.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_lists.php';
//require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_forms.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_elements.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_abtests.php';
require_once CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin_wizard.php';

class CampaignMonitorAdmin extends CampaignMonitorBase {

	public $settings;
	public $lists;
	public $forms;
	public $elements;
	public $abtests;
    public $wizard;
	public $element_types = array( 'slider', 'lightbox', 'button', 'bar', 'simple_form' );

	function __construct() {
		$this->settings = new CampaignMonitorAdminSettings();
		if ( CampaignMonitorPluginInstance()->connection->enabled() ) {
			//$this->lists    = new CampaignMonitorAdminLists();
            $this->wizard = new CampaignMonitorAdminWizard();
			//$this->forms    = new CampaignMonitorAdminForms();
			//$this->elements = new CampaignMonitorAdminElements();
			$this->abtests  = new CampaignMonitorAdminABTests();
            
		}


	}


}