<?php

use forms\core\Options;
use forms\core\Settings;

class TestSettings extends WP_UnitTestCase {

	public function test_AddSettings_ShouldSetValidArrayItem() {
		// Arrange.
		// Act.
		Settings::add( 'client_secret', 'test_secret' );      
		$cm_settings = Settings::get();

		// Assert.
		$this->assertTrue( is_array( $cm_settings ) );
		$this->assertSame( $cm_settings['client_secret'], 'test_secret' );
	}

	public function test_ClearAndReAddSettings_ShouldSetValidArrayItem() {
		// Arrange.
		Settings::clear();

		// Act.	
		Settings::add( 'client_secret', 'test_secret' );      
		$cm_settings = Settings::get();
		print_r($cm_settings);
		
		// Assert.
		$this->assertTrue( is_array( $cm_settings ) );
		$this->assertSame( $cm_settings['client_secret'], 'test_secret' );
	}

	public function test_InvalidSettingsAndAddNewSettings_ShouldReSetEmptyArrayAndAddItem() {
		// Arrange.
		Options::update( Settings::NAME, 'InvalidString' );

		// Act.
		Settings::add( 'client_secret', 'test_secret' );      
		$cm_settings = Settings::get();
		print_r($cm_settings);
		// Assert.
		$this->assertTrue( is_array( $cm_settings ) );
		$this->assertSame( $cm_settings['client_secret'], 'test_secret' );
	}

	public function test_ClearSettings_ShouldSetSettingsToEmpty() {
		// Arrange.

		// Act.		
		Settings::clear();  
		$cm_settings = Options::get( Settings::NAME );

		// Assert.		
		$this->assertSame( $cm_settings, [] );
	}

	/**
	 * @dataProvider getSettingsProvider
	 */
	public function test_GetSettings_ShouldReturnCorrectResult( $name, $expected_result ) {
		// Arrange.
		Settings::add( 'client_secret', 'test_secret' );

		// Act.
		$cm_settings = Settings::get( $name );

		// Assert.		
		$this->assertSame( $cm_settings, $expected_result );
	}

	public function getSettingsProvider() {
		return [
			[ 'invalidKey', null ],
			[ 'client_secret', 'test_secret' ],
			[ '', array( 'client_secret' => 'test_secret' ) ],
		];
	}
}
