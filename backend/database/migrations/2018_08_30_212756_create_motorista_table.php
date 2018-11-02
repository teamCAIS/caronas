<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista', function (Blueprint $table) {
			$table->increments('id');
            $table->integer('id_usuario')->unsigned();
			$table->foreign('id_usuario')->references('id')->on('pessoa');
			$table->string('modelo',7);
			$table->string('placa',7);
			$table->string('corCarro',10);
			$table->float('nota', 1, 1);
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
        Schema::dropIfExists('motorista');
    }
}
