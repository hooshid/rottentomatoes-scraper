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
        $this->assertCount(10, $result['result']);

        $this->assertEquals('The Matrix', $result['result']['title']);
        $this->assertEquals('https://www.rottentomatoes.com/m/matrix', $result['result']['full_url']);
        $this->assertEquals('movie', $result['result']['type']);
        $this->assertEquals('https://resizing.flixster.com/kO9s-jGsOi3YXyHkzVlmO9Z5lzI=/206x305/v2/https://resizing.flixster.com/hTz9Ap43sCkvDiFvCkjmb1IWkUg=/ems.cHJkLWVtcy1hc3NldHMvbW92aWVzL2EwMGEwNmQxLTE1MGYtNGQwYS04ZDhlLWQ0MzYwOTQ5M2JlMC5qcGc=', $result['result']['thumbnail']);
        $this->assertGreaterThan(100, strlen($result['result']['summary']));

        $this->assertGreaterThan(80, $result['result']['score']);
        $this->assertGreaterThan(155, $result['result']['votes']);

        $this->assertGreaterThan(80, $result['result']['user_score']);
        $this->assertGreaterThan(1307880, $result['result']['user_votes']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(6, $result['result']['cast']);
        $this->assertEquals('Keanu Reeves', $result['result']['cast'][2]['name']);
        $this->assertEquals('keanu_reeves', $result['result']['cast'][2]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/thX_rK0KiBbsHU8zYoMxN9ipq7c=/100x120/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/1443_v9_bc.jpg', $result['result']['cast'][2]['thumbnail']);

        $this->assertNull($result['error']);
    }

    public function testExtractTV()
    {
        $search = new Rottentomatoes();
        $result = $search->extract('/tv/breaking_bad');
        $this->assertIsArray($result);
        $this->assertCount(10, $result['result']);

        $this->assertEquals('Breaking Bad', $result['result']['title']);
        $this->assertEquals('https://www.rottentomatoes.com/tv/breaking_bad', $result['result']['full_url']);
        $this->assertEquals('tv', $result['result']['type']);
        $this->assertEquals('https://resizing.flixster.com/aFlnGpyD8kDQFfHsjleVKHSm4qY=/206x305/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p185846_b_v8_ad.jpg', $result['result']['thumbnail']);
        $this->assertGreaterThan(100, strlen($result['result']['summary']));

        $this->assertGreaterThan(90, $result['result']['score']);
        $this->assertGreaterThan(240, $result['result']['votes']);

        $this->assertGreaterThan(90, $result['result']['user_score']);
        $this->assertGreaterThan(20000, $result['result']['user_votes']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(6, $result['result']['cast']);
        $this->assertEquals('Vince Gilligan', $result['result']['cast'][0]['name']);
        $this->assertEquals('vince_gilligan', $result['result']['cast'][0]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/-KptjO4A26O20_TY102wK1E8wAY=/100x120/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/255299_v9_bc.jpg', $result['result']['cast'][0]['thumbnail']);

        $this->assertNull($result['error']);
    }

    public function testExtractNotFound()
    {
        $search = new Rottentomatoes();
        $result = $search->extract('/m/page_not_found');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(404, $result['error']);
    }

    public function testExtractRedirect()
    {
        $search = new Rottentomatoes();
        $result = $search->extract('/tv/that_s_so_raven');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(301, $result['error']);
    }
}
