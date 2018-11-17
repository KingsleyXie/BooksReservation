<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToReservationTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reservation', function(Blueprint $table)
		{
			$table->foreign('book0', 'reservation_ibfk_1')->references('id')->on('book')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('book1', 'reservation_ibfk_2')->references('id')->on('book')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('book2', 'reservation_ibfk_3')->references('id')->on('book')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('reservation', function(Blueprint $table)
		{
			$table->dropForeign('reservation_ibfk_1');
			$table->dropForeign('reservation_ibfk_2');
			$table->dropForeign('reservation_ibfk_3');
		});
	}

}
