<?php namespace App\Services\External;
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
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
/**
 * Class SolrFacadeAPI
 * @package App\Services\External
 */
final class SolrFacadeAPI implements ISearchApi
{

    /**
     * @param string $ctx
     * @param string $term
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function doSearchQuery($ctx, $term, $offset = 0, $limit = 10)
    {
        // http://localhost:32769/solr/www-openstack/select?indent=on&q=title:hitachi%20OR%20content:hitachi%20OR%20url:hitachi&wt=json

        try {

            $solr_host = Config::get("solr.host");
            $search_query = Config::get("solr.search_query");
            $response_fields = Config::get("solr.response_fields");
            $query_weights = Config::get("solr.query_weights");

            if(empty($solr_host))
                throw new \InvalidArgumentException("missing solr host config param!");

            $client = new Client();
            $endpoint = sprintf('%s/solr/%s/select', $solr_host, $ctx);
            $query = [
                'q' => sprintf($search_query, $term, $term, $term),
                'fl' => $response_fields,
                'wt' => 'json',
                'start' => $offset,
                'rows' => $limit
            ];

            if(!empty($query_weights)){
                $query['qf'] = $query_weights;
                $query["defType"] = "edismax";
            }

            $response = $client->get($endpoint, [
                    'query' => $query
                ]
            );

            if ($response->getStatusCode() !== 200)
                throw new Exception('invalid status code!');

            $json = $response->getBody()->getContents();
            return json_decode($json, true);
        }
        catch(RequestException $ex){
            Log::warning($ex->getMessage());
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
    public function doSuggestionQuery($ctx, $term, $offset = 0, $limit = 10)
    {
        //http://localhost:32878/solr/www-openstack/suggest?suggest=true&suggest.build=true&wt=json&suggest.q=speakers

        try {

            $solr_host = Config::get("solr.host");
            if(empty($solr_host))
                throw new \InvalidArgumentException("missing solr host config param!");

            $client = new Client();
            $endpoint = sprintf('%s/solr/%s/suggest', $solr_host, $ctx);
            $query = [
                'suggest.q' => trim($term),
                'suggest' => 'true',
                'suggest.build' => 'true',
                'wt' => 'json',
            ];

            $response = $client->get($endpoint, [
                    'query' => $query
                ]
            );

            if ($response->getStatusCode() !== 200)
                throw new Exception('invalid status code!');

            $json = $response->getBody()->getContents();
            return json_decode($json, true);
        }
        catch(RequestException $ex){
            Log::warning($ex->getMessage());
            throw $ex;
        }
    }
}