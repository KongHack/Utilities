<?php
namespace GCWorld\Utilities\Traits;

use GCWorld\Utilities\Exceptions\IPAddressException;

/**
 * Trait General
 */
trait General
{
    /**
     * @var int
     */
    protected static int $encodeOffset = 753;
    /**
     * @var int
     */
    protected static int $encodeMultiplier = 42;

    /**
     * @param int $val
     * @return string
     */
    public static function integerToTime(int $val): string
    {
        $hr  = floor($val / 60 / 60);
        $min = floor(($val - ($hr * 60 * 60)) / 60);
        $sec = floor($val - ($min * 60) - ($hr * 60 * 60));

        return str_pad($hr, 2, "0", STR_PAD_LEFT).':'.str_pad($min, 2, "0", STR_PAD_LEFT).':'.str_pad($sec, 2, "0",
            STR_PAD_LEFT);
    }

    /**
     * @param int $number
     * @return int
     */
    public static function encode(int $number): int
    {
        return (intval($number) * self::$encodeMultiplier) + self::$encodeOffset;
    }

    /**
     * @param int $number
     * @return float
     */
    public static function decode(int $number): float
    {
        return (intval($number) - self::$encodeOffset) / self::$encodeMultiplier;
    }

    /**
     * @return string
     */
    public static function getTime(): string
    {
        return number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 3).'s';
    }

    /**
     * @return string
     */
    public static function getMem(): string
    {
        return (memory_get_usage(true) / 1024).'KB';
    }

    /**
     * @return string
     *
     * @throws IPAddressException
     */
    public static function getIP(): string
    {
        if(php_sapi_name() == 'cli') {
            return '0.0.0.0';
        }

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ips = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (str_starts_with($ip, '10.')) {  //Except out internal IPs
                    continue;
                }

                return $ip;
            }

            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if (isset($_SERVER["X_FORWARDED_FOR"])) {
            return $_SERVER["X_FORWARDED_FOR"];
        }
        if (isset($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        }
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }

        throw new IPAddressException('Could Not Locate IP Address');
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function validEmail(string $email): bool
    {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain    = substr($email, $atIndex + 1);
            $local     = substr($email, 0, $atIndex);
            $localLen  = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } elseif ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } elseif ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } elseif (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } elseif (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } elseif (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * @param string $pattern
     * @param int    $flags
     * @return array
     */
    public static function glob_recursive(string $pattern, int $flags = 0): array
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::glob_recursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

    /**
     * @param string $cmd
     */
    public static function execInBackground(string $cmd): void
    {
        if (str_starts_with(php_uname(), "Windows")) {
            pclose(popen("start /B ".$cmd, "r"));
        } else {
            exec($cmd." > /dev/null &");
        }
    }

    /**
     * @param string $path
     * @return null|mixed
     */
    public static function getFileNameFromPath(string $path): null|string
    {
        return self::getLastArrayValue(explode(DIRECTORY_SEPARATOR, $path));
    }

    /**
     * @param array $arr
     * @return mixed
     */
    public static function getLastArrayValue(array $arr): mixed
    {
        if (empty($arr)) {
            return null;
        }

        return $arr[count($arr) - 1];
    }

}
