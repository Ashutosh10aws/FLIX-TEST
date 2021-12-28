<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayListPricePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('play_list_price_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('price');
            $table->integer('duration');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('play_list_price_packages');
    }
}
