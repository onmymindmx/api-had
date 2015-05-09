<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLugaresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lugares', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('nombre');
			$table->integer('categoria')->unsigned();
			$table->foreign('categoria')->references('id')->on('categorias');
			$table->integer('subcategoria')->unsigned();
			$table->foreign('subcategoria')->references('id')->on('subcategorias');
			$table->string('telefono', 15)->nullable();
			$table->text('direccion');
			$table->string('facebook', 255)->nullable();
			$table->string('twitter', 255)->nullable();
			$table->string('instagram', 255)->nullable();
			$table->string('website', 255)->nullable();
			$table->timestamps();
		});
		DB::statement('ALTER TABLE lugares ADD coordenadas POINT');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lugares');
	}

}
