<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkUserLugares extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lugares', function(Blueprint $table)
		{
			$table->integer('user')->unsigned();
			$table->foreign('user')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lugares', function(Blueprint $table)
		{
			$table->dropForeign('lugares_users_foreign');
			$table->dropColumn('user');
		});
	}

}
