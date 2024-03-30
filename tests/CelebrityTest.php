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
        $this->assertEquals('https://resizing.flixster.com/YNQWv9Xxaim-CGARtg9ptCT8e9I=/218x280/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/218027_v9_bc.jpg', $result['result']['thumbnail']);
        $this->assertGreaterThan(900, strlen($result['result']['bio']));

        $this->assertIsArray($result['result']['movies']);
        $this->assertIsArray($result['result']['series']);

        // movie data
        foreach ($result['result']['movies'] as $movie) {
            if ($movie['title'] == "Avatar") {
                $this->assertEquals('Avatar', $movie['title']);
                $this->assertEquals('/m/avatar', $movie['url']);
                $this->assertEquals('2009', $movie['year']);
                $this->assertGreaterThan(80, $movie['tomatometer']);
                $this->assertGreaterThan(80, $movie['audiencescore']);
            }
        }

        // series data
        foreach ($result['result']['series'] as $serial) {
            if ($serial['title'] == "JAG") {
                $this->assertEquals('JAG', $serial['title']);
                $this->assertEquals('/tv/jag', $serial['url']);
                $this->assertEquals('2000', $serial['year']);
                $this->assertNull($serial['tomatometer']);
                $this->assertLessThan(40, $serial['audiencescore']);
            }
        }
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
