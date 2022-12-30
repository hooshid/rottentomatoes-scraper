<?php

namespace Hooshid\RottentomatoesScraper\Base;

class Base
{
    /**
     * Get html content
     *
     * @param $url
     * @return bool|string
     */
    protected function getContentPage($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, "https://www.rottentomatoes.com/");

        $requestHeaders = array();
        $requestHeaders[] = "accept: */*";
        $requestHeaders[] = "accept-encoding: gzip, deflate, br";
        $requestHeaders[] = "accept-language: en-US,en;";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4472.124 Safari/537.36");
        $page = curl_exec($ch);
        curl_close($ch);

        return $page;
    }

    protected function getSearchContent($searchQuery, $type)
    {
        $_expiry = time() . "55";
        try {
            $req = $this->getTokenForApi($_expiry);

            $_token = $req['token'];
            $cookies = $req['cookies'];
        } catch (\Exception $e) {
            $_token = null;
            $cookies = null;
        }

        if(empty($_token)){
            return null;
        }

        $post_data = json_encode(array(
            '_expiry' => $_expiry,
            '_token' => $_token,
            'searchQuery' => $searchQuery,
            'type' => $type,
        ));

        // Prepare new cURL resource
        $ch = curl_init('https://www.rottentomatoes.com/napi/search/all');
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, "https://www.rottentomatoes.com/");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4472.124 Safari/537.36");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'accept-language: en-US,en',
                'accept-encoding: gzip, deflate, br',
                'accept: */*',
                'Content-Length: ' . strlen($post_data))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    protected function getTokenForApi($_expiry)
    {
        $post_data = json_encode(array(
            '_expiry' => $_expiry
        ));
        $ch = curl_init('https://www.rottentomatoes.com/napi/preferences/themes');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_REFERER, "https://www.rottentomatoes.com/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'accept-language: en-US,en',
                'accept-encoding: gzip, deflate, br',
                'accept: */*',
                'Content-Length: ' . strlen($post_data))
        );
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4472.124 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/"_token":"(.*)"/mi', $result, $match_token);

        // Matching the response to extract cookie value
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $match_found);

        $cookies = array();
        foreach ($match_found[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        $cookies_str = "";
        foreach ($cookies as $key => $val) {
            $cookies_str .= $key . "=" . $val . ";";
        }

        return [
            'cookies' => $cookies_str,
            'token' => $match_token[1][0],
        ];
    }

    /**
     * Clean string from html tags
     *
     * @param $str
     * @param null $remove
     * @return string|null
     */
    protected function cleanString($str, $remove = null): ?string
    {
        if (!empty($remove)) {
            $str = str_replace($remove, "", $str);
        }

        $str = str_replace("&amp;", "&", $str);
        $str = str_replace("&nbsp;", " ", $str);
        $str = str_replace("   ", " ", $str);
        $str = str_replace("  ", " ", $str);
        $str = html_entity_decode($str);

        $str = trim(strip_tags($str));
        if (empty($str)) {
            return null;
        }

        return $str;
    }

    /**
     * @return mixed|null
     */
    protected function jsonLD($html)
    {
        if (empty($html)) {
            return [];
        }

        preg_match('#<script type="application/ld\+json">(.+?)</script>#ims', $html, $matches);

        if (empty($matches[1])) {
            return [];
        }

        return json_decode($matches[1]);
    }

    /**
     * get value after last specific char
     *
     * @param $str
     * @param string $needle
     * @return string
     */
    protected function afterLast($str, string $needle = '/'): string
    {
        return substr($str, strrpos($str, $needle) + 1);
    }

    /**
     * extract numbers from string
     *
     * @param $str
     * @return int
     */
    protected function getNumbers($str): int
    {
        return (int)filter_var($str, FILTER_SANITIZE_NUMBER_INT);
    }
}