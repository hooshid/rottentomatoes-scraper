<?php

namespace Hooshid\RottentomatoesScraper;

use Hooshid\RottentomatoesScraper\Base\Base;
use voku\helper\HtmlDomParser;

class Rottentomatoes extends Base
{
    protected $baseUrl = 'https://www.rottentomatoes.com';

    protected $searchTypes = ['movie', 'tv'];

    /**
     * Search on rottentomatoes
     *
     * @param $search
     * @param $type
     * @return array|string
     */
    public function search($search, $type)
    {
        if (!in_array($type, $this->searchTypes)) {
            return 'Type can be one of this: ' . implode(", ", $this->searchTypes);
        }

        // fix typos
        $search = str_replace(":", " ", $search);
        $search = str_replace("  ", " ", $search);

        $url = $this->baseUrl . '/search?search=' . urlencode($search);
        $response = $this->getContentPage($url);

        $output = [];
        if (!empty($response)) {
            $html = HtmlDomParser::str_get_html($response);
            if ($type == "movie") {
                if ($html->findOneOrFalse("search-page-result[type='movie'] search-page-media-row")) {
                    foreach ($html->find("search-page-result[type='movie'] search-page-media-row") as $e) {
                        $title = $e->find('[data-qa="info-name"]', 0)->text();
                        $url = $e->find('[data-qa="info-name"]', 0)->getAttribute('href');
                        $thumbnail = $e->find('img', 0)->getAttribute('src');
                        $releaseYear = $e->find('search-page-media-row', 0)->getAttribute('releaseyear');
                        $score = $e->find('search-page-media-row', 0)->getAttribute('tomatometerscore');

                        if (!empty($title) and !empty($url)) {
                            $output[] = [
                                'full_url' => $this->cleanString($url),
                                'url_slug' => $this->afterLast($url),
                                'thumbnail' => $this->cleanString($thumbnail),
                                'type' => 'movie',
                                'title' => $this->cleanString($title),
                                'year' => isset($releaseYear) ? (int)$releaseYear : null,
                                'score' => isset($score) ? (int)$score : null
                            ];
                        }
                    }
                }
            } else if ($type == "tv") {
                if ($html->findOneOrFalse("search-page-result[type='tvSeries'] search-page-media-row")) {
                    foreach ($html->find("search-page-result[type='tvSeries'] search-page-media-row") as $e) {
                        $title = $e->find('[data-qa="info-name"]', 0)->text();
                        $url = $e->find('[data-qa="info-name"]', 0)->getAttribute('href');
                        $thumbnail = $e->find('img', 0)->getAttribute('src');
                        $startYear = $e->find('search-page-media-row', 0)->getAttribute('startyear');
                        $endYear = $e->find('search-page-media-row', 0)->getAttribute('endyear');
                        $score = $e->find('search-page-media-row', 0)->getAttribute('tomatometerscore');

                        if (!empty($title) and !empty($url)) {
                            $output[] = [
                                'full_url' => $this->cleanString($url),
                                'url_slug' => $this->afterLast($url),
                                'thumbnail' => $this->cleanString($thumbnail),
                                'type' => 'tv',
                                'title' => $this->cleanString($title),
                                'year' => isset($startYear) ? (int)$startYear : null,
                                'startYear' => isset($startYear) ? (int)$startYear : null,
                                'endYear' => isset($endYear) ? (int)$endYear : null,
                                'score' => isset($score) ? (int)$score : null
                            ];
                        }
                    }
                }
            }
        }

        return [
            'result' => $output
        ];
    }

    /**
     * Extract data from movie or tv page
     *
     * @param $url
     * @return array
     */
    public function extract($url): array
    {
        $output = [];
        $error = null;

        $url = str_replace("/movie/", "/m/", $url);
        $response = $this->getContentPage($this->baseUrl . $url);
        if (!empty($response)) {
            $html = HtmlDomParser::str_get_html($response);

            $type = "movie";
            if (stripos($url, "/m/") === false) {
                $type = "tv";
            }

            if ($this->cleanString($html->find('h1', 0)->innerText()) == "404 - Not Found") {
                $error = 404; // not found
            } elseif (strpos($response, 'Moved Permanently. Redirecting to') !== false) {
                $error = 301; // redirect
            }

            if (empty($error)) {
                $obj = $this->jsonLD($response);
                if (!empty($obj)) {
                    $output['title'] = isset($obj->name) ? (string)$obj->name : null;
                    $output['full_url'] = isset($obj->url) ? (string)$obj->url : null;
                    if (stripos($obj->url, "https://") === false) {
                        $output['full_url'] = $this->baseUrl . $obj->url;
                    }

                    if($type == "tv"){
                        $output['full_url'] = $this->baseUrl . $url;
                    }

                    $output['type'] = $type;

                    $output['thumbnail'] = $html->find('.posterImage, img[data-qa=poster-image], .movie-thumbnail-wrap img', 0)->getAttribute("src");

                    $output['score'] = isset($obj->aggregateRating->ratingValue) ? (int)$obj->aggregateRating->ratingValue : null;
                    $output['votes'] = isset($obj->aggregateRating->ratingCount) ? (int)$obj->aggregateRating->ratingCount : null;

                    $castContainer = "";
                    if ($type == "movie") {
                        $scoreDetailsJson = json_decode($html->find("#score-details-json", 0)->innerText());
                        $output['user_score'] = $scoreDetailsJson->scoreboard->audienceScore->value;
                        $output['user_votes'] = $scoreDetailsJson->scoreboard->audienceScore->ratingCount;

                        $castContainer = "#cast-and-crew .content-wrap .cast-and-crew-item";
                    } elseif ($type == "tv") {
                        $output['user_score'] = $this->getNumbers($html->find("score-board", 0)->getAttribute('audiencescore'));
                        $output['user_votes'] = $this->getNumbers($html->find(".scoreboard__link--audience", 0)->text());

                        $castContainer = "#cast-and-crew .cast-wrap .cast-and-crew-item";
                    }

                    $output['cast'] = [];
                    if ($html->findOneOrFalse($castContainer)) {
                        foreach ($html->find($castContainer) as $e) {
                            $url = $e->find('a', 0)->getAttribute('href');
                            $url_slug = str_replace("/celebrity/", "", $url);
                            $name = $e->find('.metadata a p', 0)->text();
                            $thumbnail = $e->find('img', 0)->getAttribute('src');
                            if (str_contains($thumbnail, 'poster_default_thumbnail') or
                                str_contains($thumbnail, 'poster-default-thumbnail')) {
                                $thumbnail = null;
                            }

                            if (!empty($url_slug) and !empty($name)) {
                                $output['cast'][] = [
                                    'name' => $this->cleanString($name),
                                    'full_url' => $this->baseUrl . $this->cleanString($url),
                                    'url_slug' => $this->cleanString($url_slug),
                                    'thumbnail' => $this->cleanString($thumbnail)
                                ];
                            }
                        }
                    }

                    $output['summary'] = $this->cleanString($html->find("[data-qa=movie-info-synopsis]", 0)->text());
                }
            }
        }

        return [
            'result' => $output,
            'error' => $error
        ];
    }

    /**
     * Extract data from celebrity page
     *
     * @param $url
     * @return array
     */
    public function celebrity($url): array
    {
        $output = [];
        $error = null;

        $url = str_replace("/celebrity/", "", $url);
        $response = $this->getContentPage($this->baseUrl . "/celebrity/" . $url);
        if (!empty($response)) {
            $html = HtmlDomParser::str_get_html($response);

            if ($this->cleanString($html->find('h1', 0)->innerText()) == "404 - Not Found") {
                $error = 404; // not found
            } elseif (strpos($response, 'Moved Permanently. Redirecting to') !== false) {
                $error = 301; // redirect
            }

            if (empty($error)) {
                $obj = $this->jsonLD($response);
                if (!empty($obj)) {
                    $output['name'] = isset($obj->name) ? (string)$obj->name : null;
                    $output['full_url'] = isset($obj->url) ? (string)$obj->url : null;
                    if (stripos($obj->url, "https://") === false) {
                        $output['full_url'] = $this->baseUrl . $obj->url;
                    }
                    $output['url_slug'] = $this->afterLast($obj->url);
                    $output['thumbnail'] = $html->find('.celebrity-bio__hero-img', 0)->getAttribute("src");
                    $output['bio'] = $this->cleanString($html->find(".celebrity-bio__summary", 0)->text());


                    $output['movies'] = [];
                    if ($html->findOneOrFalse("[data-qa='celebrity-filmography-movies']")) {
                        foreach ($html->find("[data-qa='celebrity-filmography-movies'] .celebrity-filmography__tbody tr") as $e) {
                            $url = $e->find('.celebrity-filmography__title a', 0)->getAttribute('href');
                            $title = $e->find('.celebrity-filmography__title a', 0)->text();
                            $year = $e->find('.celebrity-filmography__year', 0)->text();

                            if (!empty($url) and !empty($title)) {
                                $output['movies'][] = [
                                    'title' => $this->cleanString($title),
                                    'url' => $this->cleanString($url),
                                    'year' => $this->cleanString($year)
                                ];
                            }
                        }
                    }
                    $output['series'] = [];
                    if ($html->findOneOrFalse("[data-qa='celebrity-filmography-tv']")) {
                        foreach ($html->find("[data-qa='celebrity-filmography-tv'] .celebrity-filmography__tbody tr") as $e) {
                            $url = $e->find('.celebrity-filmography__title a', 0)->getAttribute('href');
                            $title = $e->find('.celebrity-filmography__title a', 0)->text();
                            $year = $e->find('.celebrity-filmography__year', 0)->text();
                            $year = str_replace("\n", "-", $year);
                            $year = str_replace(" ", "", $year);
                            $year = str_replace("--", "-", $year);

                            if (!empty($url) and !empty($title)) {
                                $output['series'][] = [
                                    'title' => $this->cleanString($title),
                                    'url' => $this->cleanString($url),
                                    'year' => trim($year)
                                ];
                            }
                        }
                    }
                }
            }
        }

        return [
            'result' => $output,
            'error' => $error
        ];
    }
}
