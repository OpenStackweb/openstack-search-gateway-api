<?php namespace App\Http\Controllers;
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
use App\Services\ISearchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
/**
 * Class SearchApiController
 * @package App\Http\Controllers
 */
final class SearchApiController extends JsonController
{
    /**
     * @var ISearchService
     */
    private $service;

    /**
     * SearchApiController constructor.
     * @param ISearchService $service
     */
    public function __construct(ISearchService $service)
    {
        $this->service = $service;
    }

    /**
     * @param $context
     * @param $term
     * @return mixed
     */
    public function doSearch($context, $term){
        try {
            $offset = intval(Input::get("offset", 0));
            $limit = intval(Input::get("limit", 10));
            $res = $this->service->getSearch($context, $term, $offset, $limit);
            return $this->ok($res);
        }
        catch (\Exception $ex){
            Log::error($ex);
            return $this->error500($ex);
        }
    }

    /**
     * @param $context
     * @param $term
     * @return mixed
     */
    public function getSuggestions($context, $term){
        try {
            $offset = intval(Input::get("offset", 0));
            $limit = intval(Input::get("limit", 10));
            $res = $this->service->getSuggestions($context, $term, $offset, $limit);
            return $this->ok($res);
        }
        catch (\Exception $ex){
            Log::error($ex);
            return $this->error500($ex);
        }
    }
}