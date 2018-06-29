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
use App\Exceptions\NotFoundEntityException;
use App\Models\SearchContext;
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
     * @param int $page
     * @param int $page_size
     * @return array
     * @throws Exception
     */
    public function getSearch($ctx, $term, $page = 1, $page_size = 10)
    {
        try {
            $ctx = trim($ctx);
            $term = trim($term);
            $search_context   = SearchContext::where('external_id', $ctx)->first();
            if(!$search_context)
                throw new NotFoundEntityException(sprintf("context %s not found", $ctx));

            $search_statistic = new SearchStatistic();
            $search_statistic->term = $term;
            $search_statistic->context_id = $search_context->id;
            $search_statistic->save();
            $offset = ($page - 1) * $page_size;
            $res = $this->facade->doSearchQuery($ctx, $term, $offset, $page_size);
            $response = $res['response'];
            return [
                'results' => $response['docs'],
                'qty'     => $response['numFound'],
                'offset'  => $response['start'],
                'limit'   => $page_size,
                'page'    => $page
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
    public function getSuggestions($ctx, $term, $top = 10)
    {
        try {
            $ctx = trim($ctx);
            $term = trim($term);
            $search_context   = SearchContext::where('external_id', $ctx)->first();
            if(!$search_context)
                throw new NotFoundEntityException(sprintf("context %s not found", $ctx));

            $res = $this->facade->doSuggestionQuery($ctx, $term);
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
                'qty'     => count($list),
            ];
        }
        catch (Exception $ex){
            Log::warning($ex);
            throw $ex;
        }
    }
}