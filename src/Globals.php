<?php
namespace GCWorld\Utilities;

trait Globals
{

    /**
     * @param string $key
     * @param int   $level
     *
     * @return mixed
     */
    public static function getPost(string $key, int $level = 1)
    {
        if (isset($_POST[$key])) {
            if ($level && !is_array($_POST[$key])) {
                return self::cleanString($_POST[$key], $level);
            } elseif (is_array($_POST[$key]) && $level) {
                return self::recursiveCleanArray($_POST[$key]);
            }

            return $_POST[$key];
        }
        if ($level) {
            return '';
        }

        return null;
    }

    /**
     * @param array $array
     * @param int $level
     *
     * @return mixed
     */
    public static function recursiveCleanArray(array $array, int $level = 1)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = self::recursiveCleanArray($v);
            } else {
                $array[$k] = self::cleanString($v, $level);
            }
        }

        return $array;
    }

    /**
     * @param string $string
     * @param int    $level
     *
     * @return string
     */
    public static function cleanString(string $string, int $level = 1)
    {
        if($level > 0) {
            $string = strip_tags($string);
        }
        if($level > 1) {
            $string = str_replace(["'",'"'],['',''],$string);
        }
        if($level == 3) {
            $string = htmlentities($string);
        }
        if($level > 3) {
            $string = preg_replace('/[^a-z\d _]/i', '', $string);
        }

        return trim($string);
    }

    /**
     * @param string $key
     * @param bool   $time
     *
     * @return string
     */
    public static function getPostDate(string $key, bool $time = false)
    {
        $format = 'Y-m-d'.($time ? ' H:i:s' : '');

        if (isset($_POST[$key])) {
            if (!empty($_POST[$key])) {
                return date($format, strtotime($_POST[$key]));
            }
        }

        return '0000-00-00'.($time ? ' 00:00:00' : '');
    }
}