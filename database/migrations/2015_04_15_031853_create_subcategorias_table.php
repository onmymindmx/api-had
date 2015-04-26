<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcategoriasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subcategorias', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('nombre');
			$table->text('icono');
			$table->text('descripcion');
			$table->integer('categoria')->unsigned();
			$table->foreign('categoria')->references('id')->on('categorias');
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
		Schema::drop('subcategorias');
	}

}
