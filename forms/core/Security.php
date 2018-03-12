<?php

namespace forms\core;


use forms\core\ReCaptcha\ReCaptcha;
use forms\core\ReCaptcha\RequestMethod\CurlWP;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Security
{
    public static function getCaptcha()
    {
        $publicKey = Settings::get('recaptcha_public');


        if (Security::canUseCaptcha()) {
            $html = new View();
            $html->setSitePublic($publicKey);
            $html->setLang( 'en' );
            $html->render( 'recaptcha', 'public' );
        }

    }
    
    public static function sanitize($input){
       return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public static function canUseCaptcha(){
        $recaptchaKey = Settings::get( 'recaptcha_key' );
        $recaptchaPublic = Settings::get( 'recaptcha_public' );

        return !empty( $recaptchaKey ) && !empty( $recaptchaPublic );
    }


    public static function verifyCaptcha($key, $ip)
    {
        $secret = Settings::get('recaptcha_key');
        $recaptcha = new ReCaptcha($secret,  new CurlWP());



        $response = $recaptcha->verify($key, $ip);

        if ( $response->isSuccess()) {
            return true;
        }

        Log::write($response->getErrorCodes());
        return  false;
    }

}