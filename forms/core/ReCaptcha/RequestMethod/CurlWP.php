<?php

namespace forms\core\ReCaptcha\RequestMethod;

use forms\core\ReCaptcha\RequestMethod;
use forms\core\ReCaptcha\RequestParameters;

/**
 * Sends wp_ wp_safe_remote_post to the reCAPTCHA service.
 * Note: this requires the cURL extension to be enabled in PHP
 * @see https://developer.wordpress.org/reference/functions/wp_safe_remote_post/
 */
class CurlWP implements RequestMethod
{
    /**
     * URL to which requests are sent via cURL.
     * @const string
     */
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Submit the cURL request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {

        $response = wp_safe_remote_post( CurlWP::SITE_VERIFY_URL, array(
            'body' => $params->toArray() ) );

        $response = wp_remote_retrieve_body( $response );

        $responseData = json_decode( $response );

        if (isset($responseData->success) && $responseData->success){
            return $response;
        }

        return json_encode(array('error-codes' => array($responseData)));

    }
}
