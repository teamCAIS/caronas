<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('id_corrida')->unsigned();
			$table->foreign('id_corrida')->references('id')->on('corridas');
			$table->integer('id_passageiro')->unsigned();
			$table->foreign('id_passageiro')->references('id')->on('pessoa');
			$table->float('nota', 5, 1);
			$table->integer('status_nota')->default(0);
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
        Schema::dropIfExists('historico');
    }
}
