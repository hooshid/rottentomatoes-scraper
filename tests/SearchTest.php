<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testSearchMovie()
    {
        $search = new Rottentomatoes();
        $result = $search->search('Spider-Man: Far From Home', 'movie');
        $this->assertIsArray($result);
        $this->assertCount(1, $result['result']);
        $this->assertCount(8, $result['result'][0]);

        $this->assertEquals('https://www.rottentomatoes.com/m/spider_man_far_from_home', $result['result'][0]['full_url']);
        $this->assertEquals('spider_man_far_from_home', $result['result'][0]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/8EcyD7FNd2NtugzJT1Ggvu45-kA=/fit-in/80x126/v2/https://flxt.tmsimg.com/NowShowing/177947/177947_ac.jpg', $result['result'][0]['thumbnail']);
        $this->assertEquals('movie', $result['result'][0]['type']);
        $this->assertEquals('Spider-Man: Far From Home', $result['result'][0]['title']);
        $this->assertEquals(2019, $result['result'][0]['year']);
        $this->assertGreaterThan(85, $result['result'][0]['score']);
        $this->assertGreaterThan(85, $result['result'][0]['user_score']);
    }

    public function testSearchTV()
    {
        $search = new Rottentomatoes();
        $result = $search->search('Game of Thrones', 'tv');
        $this->assertIsArray($result);
        $this->assertCount(2, $result['result']);
        $this->assertCount(10, $result['result'][0]);

        $this->assertEquals('https://www.rottentomatoes.com/tv/game_of_thrones', $result['result'][0]['full_url']);
        $this->assertEquals('game_of_thrones', $result['result'][0]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/bwl5UJxKDu79g3IGf_1og3e8SYw=/fit-in/80x126/v2/https://flxt.tmsimg.com/assets/p8553063_b_v13_ax.jpg', $result['result'][0]['thumbnail']);
        $this->assertEquals('tv', $result['result'][0]['type']);
        $this->assertEquals('Game of Thrones', $result['result'][0]['title']);
        $this->assertEquals(2011, $result['result'][0]['year']);
        $this->assertEquals(2011, $result['result'][0]['startYear']);
        $this->assertEquals(2019, $result['result'][0]['endYear']);
        $this->assertGreaterThan(85, $result['result'][0]['score']);
        $this->assertNull($result['result'][0]['user_score']);
    }
}
