<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorridaAtivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corrida_ativa', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('id_corrida')->unsigned();
			$table->foreign('id_corrida')->references('id')->on('corridas');
			$table->integer('id_passageiro')->unsigned();
			$table->foreign('id_passageiro')->references('id')->on('pessoa');
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
        Schema::dropIfExists('corrida_ativa');
    }
}
