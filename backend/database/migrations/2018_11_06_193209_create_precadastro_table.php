<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrecadastroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precadastro', function (Blueprint $table) {
            $table->increments('id');
			$table->string('nome',100);
			$table->date('nascimento');
			$table->integer('genero');
			$table->string('email',100);
			$table->string('password',200);
			$table->string('url_documento',50)->default('');
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
        Schema::dropIfExists('pessoa');
    }
}
