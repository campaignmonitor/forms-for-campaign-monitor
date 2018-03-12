<?php

namespace forms\core;

abstract class FormType
{
    const SLIDE_OUT = "slideoutTab";
    const LIGHTBOX = "lightbox";
    const BAR = "bar";
    const BUTTON = "button";
    const EMBEDDED = "embedded";

    public static function getAll()
    {
        return array(
            self::SLIDE_OUT=>   self::camelCaseToReadable(self::SLIDE_OUT),
            self::LIGHTBOX=>    self::camelCaseToReadable(self::LIGHTBOX),
            self::BAR=>         self::camelCaseToReadable(self::BAR),
            self::BUTTON=>      self::camelCaseToReadable(self::BUTTON),
            self::EMBEDDED=>    self::camelCaseToReadable(self::EMBEDDED)
        );
    }

    public static function camelCaseToReadable($str)
    {
        $str = preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $str);
        $str = ucwords($str);
        return $str;
    }

}