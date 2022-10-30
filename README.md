# Rottentomatoes Scraper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Using this Rottentomatoes API, you are able to search, browse and extract data of movies, tv series on rottentomatoes.com.

## Install
This library scrapes rottentomatoes.com so changes their site can cause parts of this library to fail. You will probably need to update a few times a year.

### Requirements
* PHP >= 7.3
* PHP cURL extension

### Install via composer
``` bash
$ composer require hooshid/rottentomatoes-scraper
```

## Run examples
The example gives you a quick demo to make sure everything's working, some sample code and lets you easily see some available data.

From the example folder in the root of this repository start up php's inbuilt webserver and browse to [http://localhost:8000]()

`php -S localhost:8000`

## Examples

### Get movie/series data
#### Movie: The Matrix (1999) / URL: https://www.rottentomatoes.com/m/matrix
``` php
$rottentomatoes = new Hooshid\RottentomatoesScraper\Rottentomatoes();
$extract = $rottentomatoes->extract("/m/matrix");
$result = $extract['result'];
$error = $extract['error'];

// get all available data as json
echo json_encode($extract);
```
in above example we first create a new obj from Rottentomatoes() class, then we call extract method and give the rottentomatoes.com url in first param.

if everything ok, result key filled and if not, the error key filled with error occurred


#### Tv Series: Game of Thrones (2011-2019) / URL: https://www.rottentomatoes.com/tv/game_of_thrones
``` php
$rottentomatoes = new Hooshid\RottentomatoesScraper\Rottentomatoes();
$extract = $rottentomatoes->extract("/tv/game_of_thrones");
$result = $extract['result'];
$error = $extract['error'];

if ($error) {
    echo $error;
} else {
    echo $result['title']; // movie/series title
    echo $result['thumbnail']; // Poster thumbnail
    echo $result['summary']; // Summary
    
    echo $result['score']; // Score
    echo number_format($result['votes']); // Votes
    echo $result['user_score']; // User Score
    echo number_format($result['user_votes']); // User Votes
}
```
you must always catch error first and get results.
in this example you can pass url without full Rottentomatoes domain!


### Search

``` php
$rottentomatoes = new Hooshid\RottentomatoesScraper\Rottentomatoes();
$result = $rottentomatoes->search("The Matrix", "movie")['result'];

foreach ($result as $row) {
    echo $row['thumbnail'];
    echo $row['title'];
    echo $row['full_url'];
    echo $row['title']; 
    echo $row['year'];
    echo $row['score']; 
    echo $row['user_score']; 
    echo $row['type'];
}
```
search method always return result key, and you just need to looped and used.
search method have two param, first the title of movie or series to search and second the type, type just can be movie or tv.

### Full examples
just open the example folder, we put all examples and methods demo for you in there!

## Related projects
* [IMDb Scraper](https://github.com/hooshid/imdb-scraper)
* [Metacritic Scraper](https://github.com/hooshid/metacritic-scraper)

## Todo
* add home list scraper

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[ico-version]: https://img.shields.io/packagist/v/hooshid/rottentomatoes-scraper.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hooshid/rottentomatoes-scraper.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hooshid/rottentomatoes-scraper
[link-downloads]: https://packagist.org/packages/hooshid/rottentomatoes-scraper
