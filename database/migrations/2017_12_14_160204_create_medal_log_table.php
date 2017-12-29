<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMedalLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('medal_log', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('event_id')->nullable()->comment('事件类型  7：获得勋章');
			$table->string('account', 50)->nullable()->comment('账户');
			$table->integer('area')->nullable()->comment('大区');
			$table->integer('timestamp')->nullable()->comment('事件产生时间');
			$table->integer('points')->nullable()->comment('勋章数量');
			$table->string('data', 250)->nullable()->comment('数据 如{
"event_id": 7,
"account": "aaaaaa@test.com",
"area": 9,
"timestamp": 1508392993,
"points": 1
}');
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
		Schema::drop('medal_log');
	}

}
