<?php

namespace forms\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class HttpClient{

    public function request($postURL, $dataToPost, $curlTYPE, $headers)
    {
        $results = '';

        $defaults = array(
            'method' => $curlTYPE,
            'timeout' => 50,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent' => 'CM_WP_PLUGIN/version;URL',
            'reject_unsafe_urls' => false,
            'blocking' => true,
            'headers' => $headers,
            'cookies' => array(),
            'body' => $dataToPost,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null,
            'limit_response_size' => null,
        );

        $response = wp_remote_request($postURL, $defaults);

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
             Log::write("Something went wrong: $error_message");
            return;
        }

        return $response['body'];

    }
}