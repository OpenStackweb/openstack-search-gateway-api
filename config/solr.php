<?php
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

return [
    'host' => env('SOLR_HOST', null),
    'search_query' => env('SOLR_SEARCH_QUERY', 'title:%1$s OR url:%1$s'),
    'response_fields'=> env('SOLR_RESPONSE_FIELDS', 'url,title,type,content'),
    'query_weights' => env('SOLR_QUERY_WEIGHTS', 'title^0.5 url^0.2')
];