<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReservationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('stuname', 60);
			$table->string('stuno', 15);
			$table->string('dorm', 15);
			$table->string('contact', 15);
			$table->string('takeday', 15);
			$table->string('taketime', 15);
			$table->timestamp('submited')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
		});

		DB::update("ALTER TABLE reservations AUTO_INCREMENT = 6001;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reservations');
	}

}
