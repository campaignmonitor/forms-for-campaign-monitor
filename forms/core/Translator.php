<?php

namespace forms\core;

abstract class Translator {

    protected $_defaultLang = 'en';

    public static function translate( $toBeTranslated, $domain = 'campaign-monitor-forms' )
    {
        return \__( $toBeTranslated, $domain );

    }

}