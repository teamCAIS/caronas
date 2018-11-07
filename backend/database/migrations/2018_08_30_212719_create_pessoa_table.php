<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePessoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pessoa', function (Blueprint $table) {
            $table->increments('id');
			$table->string('nome',100);
			$table->date('nascimento');
			$table->integer('genero');
			$table->string('email',100);
			$table->string('password',200);
			$table->integer('tipo');
			$table->string('url_foto',50)->default('');
			$table->string('codigo_validacao',8)->default('000000');
			$table->integer('status')->default(0);
            $table->timestamps();
        });
		for($i = 0; $i < 10; $i++){
			$numero_de_bytes = 4;
			$restultado_bytes = random_bytes($numero_de_bytes);
			$resultado_final = bin2hex($restultado_bytes);
			$resultado_final = strtoupper($resultado_final);
			\DB::table('pessoa')->insert(
					array(
						'nome' => '',
						'nascimento' => date('Y-m-d'),
						'genero' => rand(0,1),
						'email' => '',
						'password' => '',
						'tipo' => 0,
						'url_foto' => '',
						'codigo_validacao'=>$resultado_final,
						'status' => 0
					)
			);
		}
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
