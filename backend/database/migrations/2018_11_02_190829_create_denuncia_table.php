<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDenunciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denuncia', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('id_denunciante')->unsigned();
			$table->foreign('id_denunciante')->references('id')->on('pessoa');
			$table->integer('id_denunciado')->unsigned();
			$table->foreign('id_denunciado')->references('id')->on('pessoa');
			$table->string('tipo',20);
			$table->string('comentario',500);
			$table->date('dataDenuncia');
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
        Schema::dropIfExists('denuncia');
    }
}
