<?php

use forms\core\Ajax;
use forms\core\Application;

class TestAuthorization extends WP_UnitTestCase {

	/**
	 * Test that ajax_handler blocks subscriber-level users.
	 */
	public function test_ajax_handler_blocks_subscriber() {
		$subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber_id );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Ajax::ajax_handler();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}

	/**
	 * Test that ajax_handler blocks editor-level users.
	 */
	public function test_ajax_handler_blocks_editor() {
		$editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor_id );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Ajax::ajax_handler();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}

	/**
	 * Test that administrator has manage_options capability required by ajax_handler.
	 */
	public function test_admin_has_manage_options_for_ajax_handler() {
		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'manage_options' ) );
	}

	/**
	 * Test that handleRequest blocks subscriber-level users.
	 */
	public function test_handleRequest_blocks_subscriber() {
		$subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber_id );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Application::handleRequest();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}

	/**
	 * Test that handleRequest blocks editor-level users.
	 */
	public function test_handleRequest_blocks_editor() {
		$editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor_id );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Application::handleRequest();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}

	/**
	 * Test that administrator has manage_options capability required by handleRequest.
	 */
	public function test_admin_has_manage_options_for_handleRequest() {
		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'manage_options' ) );
	}

	/**
	 * Test that unauthenticated users are blocked by ajax_handler.
	 */
	public function test_ajax_handler_blocks_unauthenticated() {
		wp_set_current_user( 0 );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Ajax::ajax_handler();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}

	/**
	 * Test that unauthenticated users are blocked by handleRequest.
	 */
	public function test_handleRequest_blocks_unauthenticated() {
		wp_set_current_user( 0 );

		$this->assertFalse( current_user_can( 'manage_options' ) );

		$caught = false;
		try {
			Application::handleRequest();
		} catch ( \WPDieException $e ) {
			$caught = true;
			$this->assertStringContainsString( 'Unauthorized', $e->getMessage() );
		}
		$this->assertTrue( $caught, 'Expected WPDieException was not thrown' );
	}
}
