<?php

namespace forms\core;

use core\App;


/**
 * Handles ajax calls
 *
 * Class Ajax
 * @package forms\core
 */
abstract class Ajax
{

	public static $methods = array();
	protected static $actionUrl = '';


	public static function run()
	{
		if (is_admin()){
			add_action('wp_ajax_handle_ajax_cm_forms', array(__CLASS__, 'ajax_handler'));
		}

		// hook to the admin ajax section in case a user is logged in
		add_action('wp_ajax_ajax_handler_nopriv_cm_forms', array(__CLASS__, 'ajaxFormHandler'));
		// for any other user
		add_action('wp_ajax_nopriv_ajax_handler_nopriv_cm_forms', array(__CLASS__, 'ajaxFormHandler'));

		if (!empty($_POST['action']) && $_POST['action'] === 'ajax_handler_nopriv_cm_forms'){
			self::ajaxFormHandler();
		}

	}




	public static function ajax_handler()
	{
		Application::refreshTokenIfNeeded();
		// we could further optimize the plugin with one entry point for all ajax requests
		
		$response = new \stdClass();

		$requestType = Request::getPost( 'type' );
		$clientId = Request::getPost( "clientId" );

		if ($requestType === "getLists") {

			if (empty( $clientId )) {
				$response->warning = "client not found";
			} else {
				$clientLists = Application::$CampaignMonitor->get_client_list( $clientId );

				$response->clientLists = $clientLists;

			}
		}

		if ($requestType === 'get_custom_fields') {

			$selectedList = Request::getPost( 'selected_list' );

			$response->sucess = true;
			$response->custom_fields = Application::$CampaignMonitor->get_custom_fields($selectedList);

		}

		if ($requestType === 'create_custom_list') {

			$listTitle = Request::getPost( 'list_title' );
			$selectedClient = Request::getPost( 'selected_client' );
			$response->newListTitle = $listTitle;
			$response->success = true;
			$response->newList = Application::$CampaignMonitor->create_list($selectedClient,$listTitle);
		}

		if ($requestType === 'create_custom_field'){

			$selectedList = Request::getPost( 'selected_list' );
			$selectedClient = Request::getPost( 'selected_client' );
			$customFieldType = Request::getPost( 'custom_field_type' );
			$customFieldName = Request::getPost( 'custom_field_name' );

			$response->sucess = true;
			$response->result = Application::$CampaignMonitor->create_custom_field( $selectedList, $customFieldName, $customFieldType );
		}



		wp_send_json( $response );

		die();
	}

	// non authenticated users
	public static function ajax_handler_nopriv()
	{

	}

	// non authenticated users
	public static function ajaxFormHandler()
	{
		Application::refreshTokenIfNeeded();

		$response = new \stdClass();

		$recaptchaKey = Request::getPost( 'recaptcha_key' );
		$abTestId = Request::getPost( 'abTestId' );
		$customFieldsToSend = Request::getPost( 'custom_fields' );
		$formId = Request::getPost( 'formId' );
		$email = Request::getPost( 'email' );

		// google needs this
		$ip = $_SERVER['REMOTE_ADDR'];


		if (empty( $formId )) {
			die( "(code:101)" );
		}

		$customFieldAr = array();
		$formToProcess = Form::getOne( $formId );

		if (is_null($formToProcess)) {
			die( "code:102" );
		}

		// campaign monitor default list id
		$listId = $formToProcess->getCampaignMonitorListId();

		$needSecurityCheck = $formToProcess->getHasCaptcha() && Security::canUseCaptcha();

		if ($needSecurityCheck) {
			$response->verify = Security::verifyCaptcha( $recaptchaKey, $ip );

			if (!$response->verify) {
				die( "error:Captcha Error." );
			}
		}

		$formName = $formToProcess->getName();

		if (empty( $email )) {
			die( "code:108" );
		}

		$name = Request::getPost( 'name' );

		$dateOfBirth = "";
		$passedDateOfBirth = Request::getPost( 'dateOfBirth' );
		if (!empty( $passedDateOfBirth )) {
			$dob = str_replace( array( ",", "/", "." ), "-", $passedDateOfBirth );
			$dobAr = explode( "-", $dob );
			$dateIsValid = checkdate( $dobAr[0], $dobAr[1], $dobAr[2] );
			if ($dateIsValid) {
				$dateOfBirth =
					str_pad( $dobAr[2], 4, "0", STR_PAD_LEFT ) . "/" .
					str_pad( $dobAr[0], 2, "0", STR_PAD_LEFT ) . "/" .
					str_pad( $dobAr[1], 2, "0", STR_PAD_LEFT );
			} else {
				$dateOfBirthUt = strtotime( $_REQUEST["dateOfBirth"] );
				if ($dateOfBirthUt > 0) {
					$dateOfBirth = date( "Y/m/d", $dateOfBirthUt );
				}
			}
		}

		$customFieldValAr = array();

		$gender = Request::getPost( 'gender' );
		$openText = Request::getPost( 'openText' );

		if (isset( $customFieldAr["formName"] )) {
			$customFieldValAr[] = array( "Key" => $customFieldAr["formName"], "Value" => $formName . " (" . $formId . ")" );
		}
		if (!empty( $dateOfBirth ) && isset( $customFieldAr["dateOfBirth"] )) {
			$customFieldValAr[] = array( "Key" => $customFieldAr["dateOfBirth"], "Value" => $dateOfBirth );
		}
		if (!empty( $gender ) && isset( $customFieldAr["gender"] )) {
			$customFieldValAr[] = array( "Key" => $customFieldAr["gender"], "Value" => $gender );
		}
		if (!empty( $openText ) && isset( $customFieldAr["openText"] )) {
			$customFieldValAr[] = array( "Key" => $customFieldAr["openText"], "Value" => $openText );
		}



		$subscriberInfo = "";
		$isEmailUpdate = 0;
		if (is_object( $subscriberInfo )) // not found. new customer
		{
			if (isset( $subscriberInfo->EmailAddress )) {
				$isEmailUpdate = 1;
			}
		}

		if (!$isEmailUpdate) {
			if (isset( $customFieldAr["signUpSiteName"] )) {
				$customFieldValAr[] = array( "Key" => "signUpSiteName", "Value" => get_bloginfo( 'name' ) );
			}
		}

		if (!empty( $customFieldsToSend )) {

			foreach ($customFieldsToSend as $customField => $customFieldValue) {

				if (is_array( $customFieldValue ) && !empty($customFieldValue)) {


					foreach ($customFieldValue as $singleValue){
						$singleValue = Application::decode( $singleValue );
						$customFieldValAr[] = array( "Key" => $customField, "Value" => $singleValue );
					}
				} else {
					$customFieldValue = Application::decode( $customFieldValue );

					$customFieldValAr[] = array( "Key" => $customField, "Value" => $customFieldValue );

				}
			}


		}

		$dataAr = array(
			"EmailAddress" => $email,
			"Name" => $name,
			"CustomFields" => $customFieldValAr,
			"Resubscribe" => true
		, "RestartSubscriptionBasedAutoresponders" => true
		);

		$CampaignMonitor = Application::$CampaignMonitor;
		$addedEmailAddress = $CampaignMonitor->add_subscriber( $listId, $dataAr );

		$error = $CampaignMonitor->get_last_error();
		if (!empty( $error->Message )) {

			echo "error:" . $error->Message;
			die();
		} elseif (is_string( $addedEmailAddress ) && !empty( $addedEmailAddress )) {
			$response->success_message = htmlspecialchars_decode($formToProcess->getSuccessMessage(), ENT_QUOTES);
			if (!empty( $abTestId )) {
				$abTestInstance = ABTest::get( $abTestId );
				if ($abTestInstance !== null) {
					$tests = $abTestInstance->getTests();

					$ids = array();
					$index = 0;
					foreach ($tests as $test) {


						if ($test->getForm()->getId() === $formId) {

							$submissions = $test->getSubmissions();
							$abTestInstance->getTests( $index )->setSubmissions( $submissions + 1 );

						}
						$index++;
					}

					$response->ids = $ids;
					$abTestInstance->save( $abTestInstance->getId() );
				}
			}

			$response->success = $addedEmailAddress;

			// TODO refactor
			if (!empty($_POST['no_js']) && !empty($_POST['action']) && $_POST['action'] === 'ajax_handler_nopriv_cm_forms'){
				$redirectUrl = '/';

				if (!empty($_SERVER['HTTP_REFERER'])) {
					$redirectUrl = $_SERVER['HTTP_REFERER'];
				}

                echo sprintf("<div style='text-align: center; padding: 2em;'>%s<br><br>Click here to <a href='%s'>go back</a></div>", filter_var($response->success_message, FILTER_SANITIZE_STRING), filter_var($redirectUrl, FILTER_SANITIZE_STRING));
				exit();
			}

			wp_send_json( $response );
			exit();
		} else {
			echo "error:Email not added. Please try again.";
			die();
		}


		wp_send_json( $response );

	}



}