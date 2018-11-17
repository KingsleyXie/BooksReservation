<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('book', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('isbn', 15)->nullable()->unique('isbn');
			$table->string('title', 60);
			$table->string('author', 60);
			$table->string('publisher', 60);
			$table->string('pubdate', 15);
			$table->string('cover', 100)->nullable();
			$table->integer('quantity')->default(1);
			$table->timestamp('imported')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
		});

		DB::update("ALTER TABLE book AUTO_INCREMENT = 1001;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('book');
	}

}
