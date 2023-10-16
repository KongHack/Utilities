<?php
namespace GCWorld\Utilities\Traits;

/**
 * Trait Str
 */
trait Str
{
    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10): string
    {
        $characters = '0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * @param $string
     * @param $start
     * @param $end
     * @return string
     */
    public static function getStringBetween($string, $start, $end): string
    {
        $string = " ".$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return "";
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * @param $str
     * @return string
     */
    public static function xmlEntities($str): string
    {
        $xml = array('&#34;','&#38;','&#38;','&#60;','&#62;','&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;');
        $html = array('&quot;','&amp;','&amp;','&lt;','&gt;','&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
        $str = str_replace($html, $xml, $str);
        $str = str_ireplace($html, $xml, $str);

        return (string) $str;
    }

    /**
     * @param string $str
     *
     * @throws \Exception
     *
     * @return array
     */
    public static function starExplode(string $str): array
    {
        if (!is_string($str)) {
            throw new \Exception('Star Explode only works on strings!');
        }

        $arr = explode('*', trim($str, '*'));
        foreach ($arr as $k => $v) {
            if (empty($v)) {
                unset($arr[$k]);
            }
        }

        return $arr;
    }

    /**
     * @param array $arr
     *
     * @return string
     */
    public static function starImplode(array $arr): string
    {
        return '*'.implode('*', $arr).'*';
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function convertAscii(string $string): string
    {
        // Replace Single Curly Quotes
        $search[]  = chr(226).chr(128).chr(152);
        $replace[] = "'";
        $search[]  = chr(226).chr(128).chr(153);
        $replace[] = "'";

        // Replace Smart Double Curly Quotes
        $search[]  = chr(226).chr(128).chr(156);
        $replace[] = '"';
        $search[]  = chr(226).chr(128).chr(157);
        $replace[] = '"';

        // Replace En Dash
        $search[]  = chr(226).chr(128).chr(147);
        $replace[] = '--';

        // Replace Em Dash
        $search[]  = chr(226).chr(128).chr(148);
        $replace[] = '---';

        // Replace Bullet
        $search[]  = chr(226).chr(128).chr(162);
        $replace[] = '*';

        // Replace Middle Dot
        $search[]  = chr(194).chr(183);
        $replace[] = '*';

        // Replace Ellipsis with three consecutive dots
        $search[]  = chr(226).chr(128).chr(166);
        $replace[] = '...';

        // Apply Replacements
        $string = str_replace($search, $replace, $string);

        // Remove any non-ASCII Characters
        $string = preg_replace("/[^\x01-\x7F]/","", $string);

        return $string;
    }

    /**
     * @param string $search
     * @param int|null $maxItems
     *
     * @return array
     */
    public static function searchSplit(string $search, int $maxItems = null): array
    {
        $result = \str_getcsv($search, ' ');
        if (empty($result)) {
            $result = \preg_split('#\s+#', $search);
        }
        if (!\is_array($result)) {
            return [];
        }
        foreach ($result as $k => $v) {
            if (empty($v)) {
                unset($result[$k]);
            }
        }

        if($maxItems !== null) {
            return array_slice($result, 0, $maxItems);
        }

        return $result;
    }

    /**
     * @param string $name
     * @return array
     */
    public static function getNamePieces(string $name): array
    {
        // Thank ALEX for this awesome bit of REGEX!
        return \preg_split('/(?:#)/', \preg_replace('/([a-z])([A-Z])/', '$1#$2', $name), -1, PREG_SPLIT_NO_EMPTY);
    }
}
