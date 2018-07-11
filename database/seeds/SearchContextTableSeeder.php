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

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SearchContext;

/**
 * Class SearchContextTableSeeder
 */
final class SearchContextTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('search_contexts')->delete();

        SearchContext::create(
            array(
                'external_id' => 'www-openstack',
            )
        );

        SearchContext::create(
            array(
                'external_id' => 'docs-openstack',
            )
        );

        SearchContext::create(
            array(
                'external_id' => 'superuser-openstack',
            )
        );

        SearchContext::create(
            array(
                'external_id' => 'blog',
            )
        );
    }
}