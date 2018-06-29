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
use App\Exceptions\NotFoundEntityException;
use App\Services\ISearchService;
use GuzzleHttp\Exception\RequestException;
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
            $page = intval(Input::get("page", 1));
            if($page <= 0)
                return $this->error412(['invalid value for page param']);
            $page_size = intval(Input::get("page_size", 10));

            if($page_size < 10)
                return $this->error412(['invalid value for page_size param']);

            $res = $this->service->getSearch($context, $term, $page, $page_size);
            return $this->ok($res);
        }
        catch (NotFoundEntityException $ex1){
            Log::warning($ex1);
            return $this->error404();
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
            $top = intval(Input::get("top", 10));
            if($top <= 0)
                return $this->error412(['invalid value for top param']);

            $res = $this->service->getSuggestions($context, $term, $top);
            return $this->ok($res);
        }
        catch (NotFoundEntityException $ex1){
            Log::warning($ex1);
            return $this->error404();
        }
        catch (RequestException $ex2){
            Log::warning($ex2);
            return $this->error404();
        }
        catch (\Exception $ex){
            Log::error($ex);
            return $this->error500($ex);
        }
    }
}