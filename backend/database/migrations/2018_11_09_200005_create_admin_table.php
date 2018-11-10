<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
			$table->string('email',50);
			$table->string('password',200);
			$table->string('token_access',500)->default(null);
        });
		$pass = "calusaul13";
		\DB::table('admin')->insert(array('email'=>'caisequipe@gmail.com','password'=>HASH::make($pass)));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
