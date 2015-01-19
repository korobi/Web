<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeploymentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamp("start_date");
            $table->timestamp("end_date");
            $table->string("from_hash");
            $table->string("to_hash");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('deployments');
    }

}
