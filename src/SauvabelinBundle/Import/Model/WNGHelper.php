<?php

namespace SauvabelinBundle\Import\Model;

class WNGHelper
{
    public static function toDatetime($date) {

        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2]|[1-9])-(0[1-9]|[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {

            if($date === '0000-00-00')
                return null;

            $data   = explode('-', $date);

            $day    = intval($data[2]);
            $month  = intval($data[1]);
            $year   = $data[0];

            return date_create_from_format('Y-m-j', $year . "-" . $month . "-" . $day);

        }

        return null;
    }

    public static function toNumericString($string) {

        return self::isEmpty($string) ? null : intval(preg_replace("/[^0-9]/", "", $string));
    }

    public static function toEmail($string) {

        return self::isEmpty($string) ? null : filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    public static function isEmpty($string) {

        if(empty($string))
            return true;

        return str_replace(" ", "", $string) === "";
    }

    public static function similar($s1, $s2) {

        similar_text($s1, $s2, $percent);
        return $percent;
    }
}