<?php

use forms\core\Ajax;
use forms\core\Application;

class TestAuthorization extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		// Override the AJAX die handler so check_ajax_referer throws WPDieException
		// instead of calling die() directly.
		add_filter( 'wp_die_ajax_handler', array( $this, 'get_wp_die_handler' ) );
	}

	public function get_wp_die_handler( $handler = null ) {
		return array( $this, 'wp_die_handler' );
	}

	public function wp_die_handler( $message, $title = '', $args = array() ) {
		throw new \WPDieException( $message );
	}

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

	/**
	 * Test that ajax_handler rejects requests with missing/invalid nonce (CSRF protection).
	 */
	public function test_ajax_handler_rejects_invalid_nonce() {
		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'manage_options' ) );

		add_filter( 'wp_doing_ajax', '__return_true' );

		$_REQUEST['nonce'] = 'invalid_nonce';
		$_POST['nonce']    = 'invalid_nonce';

		$caught = false;
		try {
			Ajax::ajax_handler();
		} catch ( \WPDieException $e ) {
			$caught = true;
		}
		$this->assertTrue( $caught, 'Expected WPDieException for invalid nonce was not thrown' );
	}

	/**
	 * Test that ajax_handler accepts requests with a valid nonce.
	 */
	public function test_ajax_handler_accepts_valid_nonce() {
		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'manage_options' ) );

		add_filter( 'wp_doing_ajax', '__return_true' );

		$nonce = wp_create_nonce( 'cm_forms_ajax' );
		$_REQUEST['nonce'] = $nonce;
		$_POST['nonce']    = $nonce;
		$_POST['type']     = 'getLists';
		$_POST['clientId'] = '';

		// Should not throw WPDieException for auth/nonce — it will proceed
		// to the handler logic. We just verify no auth exception is thrown.
		$caught_auth = false;
		try {
			ob_start();
			Ajax::ajax_handler();
			ob_end_clean();
		} catch ( \WPDieException $e ) {
			ob_end_clean();
			// check_ajax_referer would die with -1; capability check dies with 'Unauthorized'
			if ( strpos( $e->getMessage(), 'Unauthorized' ) !== false || $e->getMessage() === '-1' ) {
				$caught_auth = true;
			}
		}
		$this->assertFalse( $caught_auth, 'Admin with valid nonce should not be auth-blocked' );
	}
}
