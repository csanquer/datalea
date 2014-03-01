<?php

namespace CSanquer\FakeryGenerator\Helper;

/**
 * Converter
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class Converter
{
    /**
     *
     * @return array
     */
    public static function getAvailableConvertMethods()
    {
        return array(
            'lowercase',
            'uppercase',
            'capitalize',
            'capitalize_words',
            'absolute',
            'as_bool',
            'as_int',
            'as_float',
            'as_string',
            'remove_accents',
            'remove_accents_lowercase',
            'remove_accents_uppercase',
            'remove_accents_capitalize',
            'remove_accents_capitalize_words',
        );
    }

    public static function tolower($str)
    {
        return mb_strtolower($str, 'UTF-8');
    }

    public static function toupper($str)
    {
        return mb_strtoupper($str, 'UTF-8');
    }

    public static function ucwords($str)
    {
        return  mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }

    public static function ucfirst($str)
    {
        $length = mb_strlen($str);
        if ($length > 1) {
            $first = mb_substr($str, 0, 1, 'UTF-8');
            $rest = mb_substr($str, 1, $length, 'UTF-8');

            return  mb_strtoupper($first, 'UTF-8').$rest;
        } else {
            return  mb_strtoupper($str, 'UTF-8');
        }
    }

    /**
     * replace accent character by normal character
     *
     * @param string $string
     * @param string $charset default = 'UTF-8'
     *
     * @return string
     */
    public static function removeAccents($string, $charset='UTF-8')
    {
        $string = htmlentities($string, ENT_NOQUOTES, $charset);
        $string = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // for ligatures e.g. '&oelig;'
        $string = html_entity_decode($string,ENT_NOQUOTES , $charset);

        return $string;
    }
}
