<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReservationTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservation', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('stuname', 60);
			$table->string('stuno', 15);
			$table->string('dorm', 15);
			$table->string('contact', 15);
			$table->string('takeday', 15);
			$table->string('taketime', 15);
			$table->integer('book0')->nullable()->index('book0');
			$table->integer('book1')->nullable()->index('book1');
			$table->integer('book2')->nullable()->index('book2');
			$table->timestamp('submited')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
		});

		DB::update("ALTER TABLE reservation AUTO_INCREMENT = 6001;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reservation');
	}

}
