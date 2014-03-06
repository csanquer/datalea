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
        return [
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
        ];
    }

    public static function convert($method, $value)
    {
        switch ($method) {
            case 'lowercase':
                $value = self::tolower($value, 'UTF-8');
                break;
            case 'uppercase':
                $value = self::toupper($value, 'UTF-8');
                break;
            case 'capitalize':
                $value = self::ucfirst($value);
                break;
            case 'capitalize_words':
                $value = self::ucwords($value);
                break;
            case 'absolute':
                $value = abs($value);
                break;
            case 'remove_accents':
                $value = self::removeAccents($value);
                break;
            case 'remove_accents_lowercase':
                $value = self::tolower(Converter::removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_uppercase':
                $value = self::toupper(Converter::removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_capitalize':
                $value = self::ucfirst(Converter::removeAccents($value));
                break;
            case 'remove_accents_capitalize_words':
                $value = self::ucwords(Converter::removeAccents($value));
                break;
            case 'as_bool':
                $value = (bool) $value;
                break;
            case 'as_int':
                $value = (int) $value;
                break;
            case 'as_float':
                $value = (float) $value;
                break;
            case 'as_string':
                $value = (string) $value;
                break;
            default:
                break;
        }

        return $value;
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
        return  mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($str, 1, mb_strlen($str), 'UTF-8');
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
        $string = preg_replace(
            [
                '#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#',
                '#&([A-za-z]{2})(?:lig);#', // for ligatures e.g. '&oelig;'
            ],
            ['\1','\1'],
            htmlentities($string, ENT_NOQUOTES, $charset)
        );

        return html_entity_decode($string, ENT_NOQUOTES , $charset);
    }
}
