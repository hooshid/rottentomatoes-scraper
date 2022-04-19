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

        $response = $this->getContentPage($this->baseUrl . '/napi/search/all?searchQuery=' . urlencode($search) . '&type=' . $type . '&f=null');
        $json = json_decode($response);

        $output = [];
        if ($type == "movie" and $json->movie) {
            $i = 0;
            foreach ($json->movie->items as $e) {
                $output[$i]['full_url'] = $e->url;
                $output[$i]['url_slug'] = $this->afterLast($e->url);
                $output[$i]['thumbnail'] = $e->imageUrl;
                $output[$i]['type'] = 'movie';
                $output[$i]['title'] = $this->cleanString($e->name);
                $output[$i]['year'] = $e->releaseYear;
                $output[$i]['score'] = @$e->criticsScore->value;
                $output[$i]['user_score'] = @($e->audienceScore->score) ? (int)$e->audienceScore->score : null;
                $i++;
            }
        } elseif ($type == "tv" and $json->tv) {
            $i = 0;
            foreach ($json->tv->items as $e) {
                $output[$i]['full_url'] = $e->url;
                $output[$i]['url_slug'] = $this->afterLast($e->url);
                $output[$i]['thumbnail'] = $e->imageUrl;
                $output[$i]['type'] = 'tv';
                $output[$i]['title'] = $this->cleanString($e->name);
                $output[$i]['year'] = $e->startYear;
                $output[$i]['startYear'] = $e->startYear;
                $output[$i]['endYear'] = $e->endYear;
                $output[$i]['score'] = @$e->criticsScore->value;
                $output[$i]['user_score'] = @($e->audienceScore->score) ? (int)$e->audienceScore->score : null;
                $i++;
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
        $response = $this->getContentPage($this->baseUrl . $url);
        $type = "movie";
        if (stripos($url, "/m/") === false) {
            $type = "tv";
        }

        $obj = $this->jsonLD($response);
        $output = [];
        $output['title'] = $obj->name;
        $output['full_url'] = $obj->url;
        if (stripos($obj->url, "https://") === false) {
            $output['full_url'] = $this->baseUrl . $obj->url;
        }
        $output['type'] = $type;

        $html = HtmlDomParser::str_get_html($response);
        $output['thumbnail'] = $html->find('.posterImage', 0)->getAttribute("data-src");

        $output['score'] = isset($obj->aggregateRating->ratingValue) ? (int)$obj->aggregateRating->ratingValue : null;
        $output['votes'] = isset($obj->aggregateRating->ratingCount) ? (int)$obj->aggregateRating->ratingCount : null;

        if ($type == "movie") {
            $scoreDetailsJson = json_decode($html->find("#score-details-json", 0)->innerText());
            $output['user_score'] = $scoreDetailsJson->scoreboard->audienceScore;
            $output['user_votes'] = $scoreDetailsJson->scoreboard->audienceCount;
        } elseif ($type == "tv") {
            $output['user_score'] = $this->getNumbers($html->find(".audience-score .mop-ratings-wrap__percentage", 0)->innerText());
            $output['user_votes'] = $this->getNumbers($html->find(".scoreboard__link--audience", 0)->text());
        }

        $output['summary'] = $this->cleanString($html->find("#movieSynopsis", 0)->text());

        return [
            'result' => $output,
        ];
    }
}
