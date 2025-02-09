<?php

namespace Hooshid\RottentomatoesScraper;

use Exception;
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

                    if ($type == "tv") {
                        $output['full_url'] = $this->baseUrl . $url;
                    }
                    $output['type'] = $type;
                    $output['thumbnail'] = $html->find('.movie-thumbnail-wrap rt-img, .media-scorecard rt-img', 0)->getAttribute("src");
                    $output['cast'] = [];
                    $output['summary'] = $obj->description ?? null;

                    try {
                        $scoreDetailsJson = json_decode($html->find("#media-scorecard-json", 0)->innerText());
                        $output['score'] = isset($scoreDetailsJson->criticsScore->score) ? (int)$scoreDetailsJson->criticsScore->score : null;
                        $output['votes'] = isset($scoreDetailsJson->criticsScore->reviewCount) ? (int)$scoreDetailsJson->criticsScore->reviewCount : null;
                        $output['user_score'] = isset($scoreDetailsJson->audienceScore->score) ? (int)$scoreDetailsJson->audienceScore->score : null;
                        if ($type == "tv") {
                            $output['user_votes'] = $this->getNumbers($scoreDetailsJson->audienceScore->bandedRatingCount);
                        } else {
                            $output['user_votes'] = isset($scoreDetailsJson->audienceScore->reviewCount) ? (int)$scoreDetailsJson->audienceScore->reviewCount : null;
                        }
                    } catch (Exception $exception) {
                        $output['score'] = null;
                        $output['votes'] = null;
                        $output['user_score'] = null;
                        $output['user_votes'] = null;
                    }

                    // cast
                    try {
                        // find emsId and load cast and crew from api
                        $castAndCrewDataJson = json_decode($html->find("#castAndCrewData", 0)->innerText());

                        if (!empty($castAndCrewDataJson) and !empty($castAndCrewDataJson->emsId)) {
                            if ($type == "tv") {
                                $apiType = "TvSeries";
                            } else {
                                $apiType = "Movie";
                            }
                            $responseCastAndCrewData = $this->getContentPage($this->baseUrl . "/cnapi/modules/cast-and-crew/" . $apiType . "/" . $castAndCrewDataJson->emsId);

                            $responseJson = json_decode($responseCastAndCrewData);
                            if ($responseJson->contentData->people) {
                                foreach ($responseJson->contentData->people as $e) {
                                    $url = $e->celebrityUrl;
                                    $url_slug = str_replace("/celebrity/", "", $url);
                                    $name = $e->name;
                                    $thumbnail = $e->primaryImageUrl;
                                    if (!$thumbnail) {
                                        $thumbnail = null;
                                    }

                                    if (!empty($url_slug) and !empty($name) and $url_slug != "undefined" and $url_slug != "null") {
                                        $output['cast'][] = [
                                            'name' => $this->cleanString($name),
                                            'full_url' => $this->baseUrl . $this->cleanString($url),
                                            'url_slug' => $this->cleanString($url_slug),
                                            'thumbnail' => $this->cleanString($thumbnail)
                                        ];
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                    }

                    if (empty($output['cast'])) {
                        try {
                            $castAndCrewDataJson = json_decode($html->find("#castAndCrewData", 0)->innerText());

                            if ($castAndCrewDataJson->people) {
                                foreach ($castAndCrewDataJson->people as $e) {
                                    $url = $e->celebrityUrl;
                                    $url_slug = str_replace("/celebrity/", "", $url);
                                    $name = $e->name;
                                    $thumbnail = $e->primaryImageUrl;
                                    if (empty($thumbnail)
                                        or strpos($thumbnail, 'poster_default_thumbnail') !== false
                                        or strpos($thumbnail, 'poster-default-thumbnail') !== false) {
                                        $thumbnail = null;
                                    }

                                    if (!empty($url_slug) and !empty($name) and $url_slug != "undefined" and $url_slug != "null") {
                                        $output['cast'][] = [
                                            'name' => $this->cleanString($name),
                                            'full_url' => $this->baseUrl . $this->cleanString($url),
                                            'url_slug' => $this->cleanString($url_slug),
                                            'thumbnail' => $this->cleanString($thumbnail)
                                        ];
                                    }
                                }
                            }
                        } catch (Exception $exception) {

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
                if (empty($obj->url)) {
                    $error = 404;
                } else if (!empty($obj)) {
                    $output['name'] = isset($obj->name) ? (string)$obj->name : null;
                    $output['full_url'] = (string)$obj->url;
                    if (stripos($obj->url, "https://") === false) {
                        $output['full_url'] = $this->baseUrl . $obj->url;
                    }
                    $output['url_slug'] = $this->afterLast($obj->url);
                    $output['thumbnail'] = $html->find('.celebrity-bio__hero-img', 0)->getAttribute("src");

                    $emsId = null;
                    try {
                        $curation = $html->find('#curation-json', 0)->innerText();
                        $emsId = json_decode($curation);
                        $emsId = $emsId->emsId;
                    } catch (\Exception $e) {

                    }

                    $output['movies'] = [];
                    $output['series'] = [];
                    if ($emsId != null) {
                        $getMovies = $this->getContentPage($this->baseUrl . "/cnapi/modules/filmography/$emsId/movie/newest?pageCount=500");
                        $data = json_decode($getMovies, true);
                        if (isset($data['media']) && is_array($data['media'])) {
                            foreach ($data['media'] as $media) {
                                $title = $media['title'] ?? '';
                                $url = $media['titleUrl'] ?? '';
                                $year = $media['yearsFeatured'] ?? '';

                                // Extract audience score and tomatometer score
                                $tomatometerScore = $media['tomatometerScore']['score'] ?? null;
                                $audienceScore = $media['audienceScore']['score'] ?? null;

                                if (!empty($title) && !empty($url)) {
                                    $output['movies'][] = [
                                        'title' => $this->cleanString($title),
                                        'url' => $this->cleanString($url),
                                        'year' => $this->cleanString($year),
                                        'tomatometer' => $tomatometerScore ? (int)$tomatometerScore : null,
                                        'audiencescore' => $audienceScore ? (int)$audienceScore : null
                                    ];
                                }
                            }
                        }


                        $getSeries = $this->getContentPage($this->baseUrl . "/cnapi/modules/filmography/$emsId/tv/newest?pageCount=500");
                        $data = json_decode($getSeries, true);
                        if (isset($data['media']) && is_array($data['media'])) {
                            foreach ($data['media'] as $media) {
                                $title = $media['title'] ?? '';
                                $url = $media['titleUrl'] ?? '';
                                $year = $media['yearsFeatured'] ?? '';
                                $year = str_replace("(", "", $year);
                                $year = str_replace(")", "", $year);
                                $year = str_replace("-Present", "", $year);
                                $year = str_replace(", ", "-", $year);
                                $year = str_replace("\n", "-", $year);
                                $year = str_replace(" ", "", $year);
                                $year = str_replace("--", "-", $year);

                                // Extract audience score and tomatometer score
                                $tomatometerScore = $media['tomatometerScore']['score'] ?? null;
                                $audienceScore = $media['audienceScore']['score'] ?? null;

                                if (!empty($title) && !empty($url)) {
                                    $output['series'][] = [
                                        'title' => $this->cleanString($title),
                                        'url' => $this->cleanString($url),
                                        'year' => trim($year),
                                        'tomatometer' => $tomatometerScore ? (int)$tomatometerScore : null,
                                        'audiencescore' => $audienceScore ? (int)$audienceScore : null
                                    ];
                                }
                            }
                        }
                    }

                    if (count($output['movies']) == 0 && count($output['series']) == 0) {
                        $bioHighestRated = $html->find('[data-qa="celebrity-bio-highest-rated"]', 0)->text();
                        $bioLowestRated = $html->find('[data-qa="celebrity-bio-lowest-rated"]', 0)->text();
                        $bioBDay = $html->find('[data-qa="celebrity-bio-bday"]', 0)->text();

                        if (trim(preg_replace("/\s+/", " ", $bioHighestRated)) == "Highest Rated: Not Available" &&
                            trim(preg_replace("/\s+/", " ", $bioLowestRated)) == "Lowest Rated: Not Available" &&
                            trim(preg_replace("/\s+/", " ", $bioBDay)) == "Birthday: Not Available") {
                            $output = [];
                            $error = 404;
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
