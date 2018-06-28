<?php namespace App\Services;
/**
 * Copyright 2018 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
use App\Models\SearchStatistic;
use App\Services\External\ISearchApi;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class SearchService
 * @package App\Services
 */
final class SearchService implements ISearchService
{
    /**
     * @var ISearchApi
     */
    private $facade;

    /**
     * SearchService constructor.
     * @param ISearchApi $facade
     */
    public function __construct(ISearchApi $facade)
    {
        $this->facade = $facade;
    }

    /**
     * @param string $ctx
     * @param string $term
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getSearch($ctx, $term, $offset = 0, $limit = 10)
    {
        try {

            $term = trim($term);
            $search_statistic = SearchStatistic::where('term', $term)->first();
            if(!$search_statistic) {
                $search_statistic = new SearchStatistic();
                $search_statistic->hits = 0;
            }
            $search_statistic->term = $term;
            $search_statistic->hits = $search_statistic->hits + 1;
            $search_statistic->save();

            $res = $this->facade->doSearchQuery($ctx, $term, $offset, $limit);
            $response = $res['response'];
            return [
                'results' => $response['docs'],
                'qty'     => $response['numFound'],
                'offset'  => $response['start'],
                'limit'   => $limit
            ];
        }
        catch (Exception $ex){
            Log::warning($ex);
            throw $ex;
        }
    }

    /**
     * @param string $ctx
     * @param string $term
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getSuggestions($ctx, $term, $offset = 0, $limit = 10)
    {
        try {
            $term = trim($term);
            $res = $this->facade->doSuggestionQuery($ctx, $term, $offset, $limit);
            $suggest_res = $res['suggest'];
            $dic = [];
            $list = [];
            foreach ($suggest_res as $engine => $engine_suggestions) {
                foreach ($engine_suggestions as $term => $results) {
                    if (isset($results['numFound']) && intval($results['numFound']) == 0) continue;
                    $suggestions = $results['suggestions'];
                    foreach ($suggestions as $entry) {
                        if (isset($dic[$entry['payload']])) continue;

                        $dic[$entry['payload']] = $entry['payload'];
                        $list[] = $entry;
                    }
                }
            }
            return [
                'results' => $list,
                'qty' => count($list),
            ];
        }
        catch (Exception $ex){
            Log::warning($ex);
            throw $ex;
        }
    }
}