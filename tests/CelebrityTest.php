<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class CelebrityTest extends TestCase
{
    public function testCelebrity()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('tom_cruise');
        $this->assertIsArray($result);
        $this->assertCount(7, $result['result']);
        $this->assertNull($result['error']);

        $this->assertEquals('Tom Cruise', $result['result']['name']);
        $this->assertEquals('https://www.rottentomatoes.com/celebrity/tom_cruise', $result['result']['full_url']);
        $this->assertEquals('tom_cruise', $result['result']['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/aKZD0ZOIIhT1YuBxFNJxe20iqYM=/218x280/v2/https://resizing.flixster.com/vpi-xtSdBvad3lgHJ9aMFwnZXNo=/ems.cHJkLWVtcy1hc3NldHMvY2VsZWJyaXRpZXMvMmYxODg5MzMtODE3MS00ZTI2LThmYmYtZGVmMzE4MmI3MjRiLmpwZw==', $result['result']['thumbnail']);
        $this->assertGreaterThan(3500, strlen($result['result']['bio']));

        $this->assertIsArray($result['result']['movies']);
        $this->assertIsArray($result['result']['series']);

        // movie data
        foreach ($result['result']['movies'] as $movie) {
            if ($movie['title'] == "Mission: Impossible - Dead Reckoning Part One") {
                $this->assertEquals('Mission: Impossible - Dead Reckoning Part One', $movie['title']);
                $this->assertEquals('/m/mission_impossible_dead_reckoning_part_one', $movie['url']);
                $this->assertEquals('2023', $movie['year']);
                $this->assertGreaterThan(90, $movie['tomatometer']);
                $this->assertGreaterThan(90, $movie['audiencescore']);
            } else if ($movie['title'] == "Taps") {
                $this->assertEquals('Taps', $movie['title']);
                $this->assertEquals('/m/taps', $movie['url']);
                $this->assertEquals('1981', $movie['year']);
                $this->assertGreaterThan(60, $movie['tomatometer']);
                $this->assertGreaterThan(60, $movie['audiencescore']);
            }
        }

        // series data
        foreach ($result['result']['series'] as $serial) {
            if ($serial['title'] == "Top Gear") {
                $this->assertEquals('Top Gear', $serial['title']);
                $this->assertEquals('/tv/top-gear', $serial['url']);
                $this->assertEquals('2010', $serial['year']);
                $this->assertNull($serial['tomatometer']);
                $this->assertLessThan(70, $serial['audiencescore']);
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
