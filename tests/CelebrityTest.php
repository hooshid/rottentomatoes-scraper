<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class CelebrityTest extends TestCase
{
    public function testCelebrity()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('sam_worthington');
        $this->assertIsArray($result);
        $this->assertCount(7, $result['result']);
        $this->assertNull($result['error']);

        $this->assertEquals('Sam Worthington', $result['result']['name']);
        $this->assertEquals('https://www.rottentomatoes.com/celebrity/sam_worthington', $result['result']['full_url']);
        $this->assertEquals('sam_worthington', $result['result']['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/67DBf2tgYm7lfbd6qE4Xv1r9AAo=/218x280/v2/https://flxt.tmsimg.com/assets/218027_v9_bb.jpg', $result['result']['thumbnail']);
        $this->assertGreaterThan(900, strlen($result['result']['bio']));

        $this->assertIsArray($result['result']['movies']);
        $this->assertIsArray($result['result']['series']);
    }

    public function testCelebrityNotFound()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('page_not_found');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(404, $result['error']);
    }
}