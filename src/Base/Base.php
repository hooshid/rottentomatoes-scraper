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

        curl_setopt($ch, CURLOPT_USERAGENT, "spider");
        $page = curl_exec($ch);
        curl_close($ch);

        return $page;
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
        if (empty($str)) {
            return null;
        }
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