<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CampaignMonitorBase {
	function renderTemplate ( $template ) {
		$templateFile = $template . '.php';

		if ( file_exists( CAMPAIGN_MONITOR_TEMPLATES_FOLDER . $templateFile ) ) {
			include( CAMPAIGN_MONITOR_TEMPLATES_FOLDER . $templateFile );
			return true;
		}

		return false;
	}

	function sanitize_array( $data ) {
		if ( is_array( $data ) ){
			foreach( $data as $key => $value ) {
				if ( is_array( $value ) ) {
					$data[$key] = $this->sanitize_array( $value );
				} else {
					$data[$key] = sanitize_text_field( $value );
				}
			}
		}

		return $data;
	}
}