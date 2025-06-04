<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testSearchMovie()
    {
        $rottenTomatoes = new Rottentomatoes();
        $result = $rottenTomatoes->search('Spider-Man: Far From Home', 'movie');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['result']);
        $this->assertCount(7, $result['result'][0]);

        $this->assertEquals('https://www.rottentomatoes.com/m/spider_man_far_from_home', $result['result'][0]['full_url']);
        $this->assertEquals('spider_man_far_from_home', $result['result'][0]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/Rn71v_leWc5l2j938mr6QL7bJJk=/fit-in/80x126/v2/https://resizing.flixster.com/Fc1FZrej9A-GYxYP3boYzqIQ9Tk=/ems.cHJkLWVtcy1hc3NldHMvbW92aWVzLzRmYzI5Yzc5LWI1NDUtNDk5OC1hNjBkLTRlNGNhZTRjNWE4OS53ZWJw', $result['result'][0]['thumbnail']);
        $this->assertEquals('movie', $result['result'][0]['type']);
        $this->assertEquals('Spider-Man: Far From Home', $result['result'][0]['title']);
        $this->assertEquals(2019, $result['result'][0]['year']);
        $this->assertGreaterThan(85, $result['result'][0]['score']);
    }

    public function testSearchMovieResultCount()
    {
        $rottenTomatoes = new Rottentomatoes();
        $result = $rottenTomatoes->search('Pirates of the Caribbean', 'movie');

        $this->assertIsArray($result);
        $this->assertCount(7, $result['result']);
        $this->assertCount(7, $result['result'][0]);
    }

    public function testSearchTV()
    {
        $rottenTomatoes = new Rottentomatoes();
        $result = $rottenTomatoes->search('Game of Thrones', 'tv');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['result']);
        $this->assertCount(9, $result['result'][0]);

        $this->assertEquals('https://www.rottentomatoes.com/tv/game_of_thrones', $result['result'][0]['full_url']);
        $this->assertEquals('game_of_thrones', $result['result'][0]['url_slug']);
        $this->assertEquals('https://resizing.flixster.com/kXSybtJvZlV6mXrfqhmerZwlIaA=/fit-in/80x126/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p8553063_b_v13_ax.jpg', $result['result'][0]['thumbnail']);
        $this->assertEquals('tv', $result['result'][0]['type']);
        $this->assertEquals('Game of Thrones', $result['result'][0]['title']);
        $this->assertEquals(2011, $result['result'][0]['year']);
        $this->assertEquals(2011, $result['result'][0]['startYear']);
        $this->assertEquals(2019, $result['result'][0]['endYear']);
        $this->assertGreaterThan(85, $result['result'][0]['score']);
    }

    public function testSearchNotFound()
    {
        $rottenTomatoes = new Rottentomatoes();
        $result = $rottenTomatoes->search('mmmmmmmmm', 'movie');

        $this->assertIsArray($result);
        $this->assertCount(0, $result['result']);
    }
}
