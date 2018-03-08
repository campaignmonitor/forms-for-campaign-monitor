<?php

namespace forms\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class HtmlFields
{

    public static function text($var, $val="", $more="")
    {
        if (!empty($more))
        {
            $more=' '.trim($more);
        }
        return '<input type="text" name="'.htmlentities($var).'" value="'.html_entity_decode(htmlentities($val)).'"'.$more.' />';
    }

    public static function textArea($var, $val="", $more="")
    {
        if (!empty($more))
        {
            $more=' '.trim($more);
        }
        return '<textarea name="'.htmlentities($var).'"'.$more.' />'.htmlentities($val).'</textarea>';
    }

    public static function checkBox($var, $is_checked=0,$val=1, $more="")
    {
        if (!empty($more))
        {
            $more=' '.trim($more);
        }
        $c='';
        if ($is_checked)
        {
            $c=' checked="checked"';
        }
        return '<input type="checkbox" name="'.htmlentities($var).'" value="'.htmlentities($val).'"'.$c.$more.' />';
    }

    public static function select($var, $options, $val="", $more="")
    {
        if (!empty($more))
        {
            $more=' '.trim($more);
        }
        $str='<select name="'.htmlentities($var).'"'.$more.'>';
        foreach ($options as $k=>$v)
        {
            $sel='';
            if ($k==$val)
            {
                $sel=' selected="selected"';
            }
            $str.="<option value=\"".htmlentities($k)."\"".$sel.">".htmlentities($v)."</option>\n";
        }
        $str.='</select>';
        return $str;
    }

    public static function hidden($var, $val="", $more="")
    {
        if (!empty($more))
        {
            $more=' '.trim($more);
        }
        return '<input type="hidden" name="'.htmlentities($var).'" value="'.htmlentities($val).'"'.$more.' />';
    }
}