<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenhasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senhas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_id')->unsigned();
            $table->foreign('tipo_id')->references('id')->on('tipos')->onDelete('cascade');
            $table->integer('numero');
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
        Schema::dropIfExists('senhas');
    }
}
