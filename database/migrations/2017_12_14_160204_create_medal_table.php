<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMedalTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('medal', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('account', 50);
			$table->string('nick', 50);
			$table->boolean('area');
			$table->integer('points');
			$table->integer('timestamp');
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
		Schema::drop('medal');
	}

}
