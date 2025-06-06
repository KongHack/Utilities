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
     * @param array  $fields
     * @return string
     */
    public static function post(string $url, array $fields): string
    {
        $curl = curl_init();

        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, count($fields));
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
     * @param array  $fields
     * @return string
     */
    public static function postRaw(string $url, array $fields): string
    {
        $command = 'curl '.$url.' \\';
        foreach ($fields as $k => $v) {
            $command .= "\n".' -d \''.$k.'='.$v.'\' \\';
        }
        $command = substr($command, 0, -2).';';

        return shell_exec($command);
    }

    /**
     * @param string $url
     * @param string $data
     *
     * @return string
     */
    public static function postStringRaw(string $url, string $data): string
    {
        $command = 'curl --data \''.$data.'\' '.$url;
        $command = $command.';';

        return shell_exec($command);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getCF(string $url): string
    {
        //get cloudflare ChallengeForm
        $data = self::openURLCF($url);
        preg_match('/<form id="ChallengeForm" .+ name="act" value="(.+)".+name="jschl_vc" value="(.+)".+<\/form>.+jschl_answer.+\(([0-9\+\-\*]+)\);/Uis', $data, $out);
        if (count($out)>0) {
            eval("\$jschl_answer=$out[3];");
            $post['act']             = $out[1];
            $post['jschl_vc']        = $out[2];
            // $post['jschl_answer']    = $jschl_answer;
            //send jschl_answer to the website
            $data = self::openURLCF($url, $post);
        }

        return($data);
    }

    /**
     * @param string $url
     * @param array  $post
     * @return string
     */
    protected static function openURLCF(string $url, array $post = []): string
    {
        $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1';
        $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
        $headers[] = 'Accept-Language: ar,en;q=0.5';
        $headers[] = 'Connection: keep-alive';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (count($post)>0) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/curl.cookie');
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/curl.cookie');
        $data = curl_exec($ch);

        return($data);
    }
}
