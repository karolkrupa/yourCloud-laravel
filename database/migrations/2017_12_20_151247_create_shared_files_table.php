<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_users_id')->unsigned();
            $table->integer('target_users_id')->unsigned();
            $table->integer('files_id')->unsigned();
            $table->integer('privileges')->unsigned();
            $table->timestamps();

            $table->foreign('provider_users_id')->references('id')->on('users');
            $table->foreign('target_users_id')->references('id')->on('users');
            $table->foreign('files_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared_files');
    }
}
