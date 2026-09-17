<?php

namespace GCWorld\Utilities\Traits;

/**
 * Trait Curl
 */
trait Curl
{
    /**
     * @param string $url
     * @return string
     */
    public static function get(string $url): string
    {
        $curl = curl_init();

        $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";

        $refs = array("google.com", "yahoo.com", "msn.com", "ask.com", "live.com", "facebook.com");
        $choice = array_rand($refs);
        $refs = "http://" . $refs[$choice] . "";

        $browsers = array("Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.7 (KHTML, like Gecko) Flock/3.5.3.4628 Chrome/7.0.517.450 Safari/534.7", //Latest FLOCK Browser (12/05/2011)
            "Mozilla/5.0 (compatible; Konqueror/4.5; FreeBSD) KHTML/4.5.4 (like Gecko)",    //Latest Konqueror (12/05/2011)
            "Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))",                   //IE 9 (12/05/2011)
            "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0a2) Gecko/20110613 Firefox/6.0a2"    //Firefox 6.0a2 (12/05/2011)
        );
        $choice2 = array_rand($browsers);
        $browser = $browsers[$choice2];

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $browser);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, $refs);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($curl);

        if ($data === false) {
            $data = '';
        }
        curl_close($curl);

        return $data;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getFast(string $url): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: */*"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($curl);

        if ($data === false) {
            $data = '';
        }
        curl_close($curl);

        return $data;
    }

    /**
     * @param string $url
     * @return string|int
     */
    public static function head(string $url): string|int
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
        curl_exec($curl);

        $retcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $retcode;
    }

    /**
     * @param string $url
     * @param array<array-key, scalar|null> $fields
     * @return string
     */
    public static function post(string $url, array $fields): string
    {
        $curl = curl_init();

        $fields_string = http_build_query($fields, '', '&', PHP_QUERY_RFC3986);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, (bool) count($fields));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($curl);
        curl_close($curl);
        if ($data === false) {
            $data = '';
        }

        return $data;
    }

    /**
     * @param string $url
     * @param array<array-key, scalar|null> $fields
     * @return string
     */
    public static function postRaw(string $url, array $fields): string
    {
        return self::post($url, $fields);
    }

    /**
     * @param string $url
     * @param string $data
     *
     * @return string
     */
    public static function postStringRaw(string $url, string $data): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($curl);
        curl_close($curl);

        return false === $response ? '' : $response;
    }
}
