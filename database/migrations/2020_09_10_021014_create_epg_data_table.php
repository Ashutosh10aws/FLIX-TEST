<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpgDataTable extends Migration
{
    public function up()
    {
        Schema::create('epg_data', function (Blueprint $table) {
            $table->id();
            $table->string('start')->nullable();
            $table->string('stop')->nullable();
            $table->string('channel_id')->nullable();
            $table->text('title')->nullable();
            $table->text('desc')->nullable();
            $table->string('category')->nullable();
            $table->integer('epg_code_id');
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
        Schema::dropIfExists('epg_data');
    }
}
