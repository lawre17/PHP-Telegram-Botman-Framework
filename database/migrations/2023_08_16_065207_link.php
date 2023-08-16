<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Link extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('link', function (Blueprint $table) {
            $table->increments('id');
            $table->text('apikey')->nullable();
            $table->text('code')->nullable();
            $table->string('uname')->nullable();
            $table->string('pwd')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phno')->unique()->nullable();
            $table->string('chatid')->unique()->nullable();
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
        Schema::dropIfExists('link');
    }
}
