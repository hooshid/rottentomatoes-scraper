<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class ExtractTest extends TestCase
{
    public function testExtractMovie()
    {
        $search = new Rottentomatoes();
        $result = $search->extract('/m/matrix');
        $this->assertIsArray($result);
        $this->assertCount(9, $result['result']);

        $this->assertEquals('The Matrix', $result['result']['title']);
        $this->assertEquals('https://www.rottentomatoes.com/m/matrix', $result['result']['full_url']);
        $this->assertEquals('movie', $result['result']['type']);
        $this->assertEquals('https://resizing.flixster.com/q1aWnhA588SmGiAht_9L3KXFUMA=/206x305/v2/https://flxt.tmsimg.com/assets/p22804_p_v8_av.jpg', $result['result']['thumbnail']);

        $this->assertGreaterThan(85, $result['result']['score']);
        $this->assertGreaterThan(155, $result['result']['votes']);

        $this->assertGreaterThan(80, $result['result']['user_score']);
        $this->assertGreaterThan(33324200, $result['result']['user_votes']);
    }

    public function testExtractTV()
    {
        $search = new Rottentomatoes();
        $result = $search->extract('/tv/breaking_bad');
        $this->assertIsArray($result);
        $this->assertCount(9, $result['result']);

        $this->assertEquals('Breaking Bad', $result['result']['title']);
        $this->assertEquals('https://www.rottentomatoes.com/tv/breaking_bad', $result['result']['full_url']);
        $this->assertEquals('tv', $result['result']['type']);
        $this->assertEquals('https://resizing.flixster.com/l9rkDdf5Arg5Ffiq_Q22LsKC76w=/206x305/v2/https://flxt.tmsimg.com/assets/p185846_b_v8_ad.jpg', $result['result']['thumbnail']);

        $this->assertGreaterThan(95, $result['result']['score']);
        $this->assertGreaterThan(240, $result['result']['votes']);

        $this->assertGreaterThan(95, $result['result']['user_score']);
        $this->assertEquals(0, $result['result']['user_votes']);
    }
}
