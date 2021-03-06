<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSearchStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_statistics', function($table)
        {
            $table->bigIncrements('id');
            $table->string('term', 255)->nullable()->default('');
            $table->bigInteger("context_id")->unsigned()->nullable();
            $table->index('context_id');
            $table->foreign('context_id')->references('id')->on('search_contexts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('search_statistics');
    }
}
