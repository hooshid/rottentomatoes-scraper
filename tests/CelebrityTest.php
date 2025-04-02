<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class CelebrityTest extends TestCase
{
    public function testCelebrityTomCruise()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('tom_cruise');

        // Assert the overall structure
        $this->assertIsArray($result);
        $this->assertCount(6, $result['result']);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertNull($result['error']);

        // Assert celebrity details
        $this->assertEquals('Tom Cruise', $result['result']['name']);
        $this->assertEquals('https://www.rottentomatoes.com/celebrity/tom_cruise', $result['result']['full_url']);
        $this->assertEquals('tom_cruise', $result['result']['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/aKZD0ZOIIhT1YuBxFNJxe20iqYM=/218x280/v2/https://resizing.flixster.com/vpi-xtSdBvad3lgHJ9aMFwnZXNo=/ems.cHJkLWVtcy1hc3NldHMvY2VsZWJyaXRpZXMvMmYxODg5MzMtODE3MS00ZTI2LThmYmYtZGVmMzE4MmI3MjRiLmpwZw==', $result['result']['thumbnail']);

        // Assert movies array
        $this->assertIsArray($result['result']['movies']);
        $this->assertGreaterThan(55, count($result['result']['movies']));

        // Assert series array
        $this->assertIsArray($result['result']['series']);
        $this->assertGreaterThan(10, count($result['result']['series']));

        // Test specific movie data
        foreach ($result['result']['movies'] as $movie) {
            if ($movie['title'] == "Mission: Impossible - Dead Reckoning Part One") {
                $this->assertEquals('Mission: Impossible - Dead Reckoning Part One', $movie['title']);
                $this->assertEquals('/m/mission_impossible_dead_reckoning_part_one', $movie['url']);
                $this->assertEquals('2023', $movie['year']);
                $this->assertGreaterThan(90, $movie['tomatometer']);
                $this->assertGreaterThan(90, $movie['audiencescore']);
            } else if ($movie['title'] == "Top Gun: Maverick") {
                $this->assertEquals('Top Gun: Maverick', $movie['title']);
                $this->assertEquals('/m/top_gun_maverick', $movie['url']);
                $this->assertEquals('2022', $movie['year']);
                $this->assertGreaterThan(95, $movie['tomatometer']);
                $this->assertGreaterThan(95, $movie['audiencescore']);
            } else if ($movie['title'] == "The Merv Griffin Show - 40 of the Most Interesting People of Our Time") {
                $this->assertEquals('The Merv Griffin Show - 40 of the Most Interesting People of Our Time', $movie['title']);
                $this->assertEquals('/m/merv_griffin_show_40_of_the_most_interesting_people_of_our_time', $movie['url']);
                $this->assertEquals('2006', $movie['year']);
                $this->assertNull($movie['tomatometer']);
                $this->assertNull($movie['audiencescore']);
            } else if ($movie['title'] == "Legend") {
                $this->assertEquals('Legend', $movie['title']);
                $this->assertEquals('/m/1012164-legend', $movie['url']);
                $this->assertEquals('1985', $movie['year']);
                $this->assertGreaterThan(40, $movie['tomatometer']);
                $this->assertGreaterThan(70, $movie['audiencescore']);
            } else if ($movie['title'] == "Taps") {
                $this->assertEquals('Taps', $movie['title']);
                $this->assertEquals('/m/taps', $movie['url']);
                $this->assertEquals('1981', $movie['year']);
                $this->assertGreaterThan(60, $movie['tomatometer']);
                $this->assertGreaterThan(60, $movie['audiencescore']);
            }
        }

        // Test specific series data
        foreach ($result['result']['series'] as $serial) {
            if ($serial['title'] == "Top Gear") {
                $this->assertEquals('Top Gear', $serial['title']);
                $this->assertEquals('/tv/top-gear', $serial['url']);
                $this->assertEquals('2010', $serial['year']);
                $this->assertNull($serial['tomatometer']);
                $this->assertLessThan(70, $serial['audiencescore']);
            } elseif ($serial['title'] == "Fallen Angels") {
                $this->assertEquals('Fallen Angels', $serial['title']);
                $this->assertEquals('/tv/fallen_angels', $serial['url']);
                $this->assertEquals('1993', $serial['year']);
                $this->assertNull($serial['tomatometer']);
                $this->assertNull($serial['audiencescore']);
            }
        }
    }

    public function testCelebrityBryanCranston()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('bryan_cranston');

        // Assert the overall structure
        $this->assertIsArray($result);
        $this->assertCount(6, $result['result']);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertNull($result['error']);

        // Assert celebrity details
        $this->assertEquals('Bryan Cranston', $result['result']['name']);
        $this->assertEquals('https://www.rottentomatoes.com/celebrity/bryan_cranston', $result['result']['full_url']);
        $this->assertEquals('bryan_cranston', $result['result']['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/kEIv62-pIv0_YmbAF8969V05VIs=/218x280/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/164311_v9_bc.jpg', $result['result']['thumbnail']);

        // Assert movies array
        $this->assertIsArray($result['result']['movies']);
        $this->assertGreaterThan(60, count($result['result']['movies']));

        // Assert series array
        $this->assertIsArray($result['result']['series']);
        $this->assertGreaterThan(79, count($result['result']['series']));

        // Test specific movie data
        foreach ($result['result']['movies'] as $movie) {
            if ($movie['title'] == "Kung Fu Panda 4") {
                $this->assertEquals('Kung Fu Panda 4', $movie['title']);
                $this->assertEquals('/m/kung_fu_panda_4', $movie['url']);
                $this->assertEquals('2024', $movie['year']);
                $this->assertEquals(71, $movie['tomatometer']);
                $this->assertEquals(85, $movie['audiencescore']);
            } elseif ($movie['title'] == "Drive") {
                $this->assertEquals('Drive', $movie['title']);
                $this->assertEquals('/m/drive_2011', $movie['url']);
                $this->assertEquals('2011', $movie['year']);
                $this->assertEquals(93, $movie['tomatometer']);
                $this->assertEquals(79, $movie['audiencescore']);
            } elseif ($movie['title'] == "The Lincoln Lawyer") {
                $this->assertEquals('The Lincoln Lawyer', $movie['title']);
                $this->assertEquals('/m/lincoln_lawyer', $movie['url']);
                $this->assertEquals('2011', $movie['year']);
                $this->assertEquals(83, $movie['tomatometer']);
                $this->assertEquals(82, $movie['audiencescore']);
            }
        }

        // Test specific series data
        foreach ($result['result']['series'] as $serial) {
            if ($serial['title'] == "Your Honor") {
                $this->assertEquals('Your Honor', $serial['title']);
                $this->assertEquals('/tv/your_honor_2020', $serial['url']);
                $this->assertEquals('2020-2021-2023-2025', $serial['year']);
                $this->assertEquals(49, $serial['tomatometer']);
                $this->assertEquals(68, $serial['audiencescore']);
            } elseif ($serial['title'] == "Breaking Bad") {
                $this->assertEquals('Breaking Bad', $serial['title']);
                $this->assertEquals('/tv/breaking_bad', $serial['url']);
                $this->assertEquals('2008-2013', $serial['year']);
                $this->assertEquals(96, $serial['tomatometer']);
                $this->assertEquals(97, $serial['audiencescore']);
            } elseif ($serial['title'] == "Modern Family") {
                $this->assertEquals('Modern Family', $serial['title']);
                $this->assertEquals('/tv/modern_family', $serial['url']);
                $this->assertEquals('2012-2013', $serial['year']);
                $this->assertEquals(85, $serial['tomatometer']);
                $this->assertEquals(90, $serial['audiencescore']);
            } elseif ($serial['title'] == "The Simpsons") {
                $this->assertEquals('The Simpsons', $serial['title']);
                $this->assertEquals('/tv/the_simpsons', $serial['url']);
                $this->assertEquals('2012-2013', $serial['year']);
                $this->assertEquals(85, $serial['tomatometer']);
                $this->assertEquals(75, $serial['audiencescore']);
            } elseif ($serial['title'] == "60 Minutes") {
                $this->assertEquals('60 Minutes', $serial['title']);
                $this->assertEquals('/tv/60_minutes', $serial['url']);
                $this->assertEquals('2016-2017', $serial['year']);
                $this->assertNull($serial['tomatometer']);
                $this->assertNull($serial['audiencescore']);
            }
        }
    }

    public function testCelebrityNoSeries()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('gore_verbinski');

        // Assert the overall structure
        $this->assertIsArray($result);
        $this->assertCount(6, $result['result']);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertNull($result['error']);

        // Assert movies array
        $this->assertIsArray($result['result']['movies']);
        $this->assertGreaterThan(10, count($result['result']['movies']));

        // Assert series array
        $this->assertIsArray($result['result']['series']);
        $this->assertCount(0, $result['result']['series']);
        $this->assertEmpty($result['result']['series']);
    }

    public function testCelebrityBranislavLecic()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('branislav_lecic');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(404, $result['error']);
    }

    public function testCelebrityNotFound()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('page_not_found');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(404, $result['error']);
    }

    public function testCelebrityInFinished()
    {
        $search = new Rottentomatoes();
        $result = $search->celebrity('bruce_meyers');

        $this->assertIsArray($result);
        $this->assertEmpty($result['result']);
        $this->assertEquals(404, $result['error']);
    }
}
