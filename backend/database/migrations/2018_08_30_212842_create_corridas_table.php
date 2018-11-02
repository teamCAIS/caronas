<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorridasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corridas', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('id_motorista')->unsigned();
			$table->foreign('id_motorista')->references('id')->on('motorista');
			$table->string('saida',20);
			$table->string('pontoEncontro',100);
			$table->integer('vagas');
			$table->timestamp('data_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('status')->default(0);
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
        Schema::dropIfExists('corridas');
    }
}
