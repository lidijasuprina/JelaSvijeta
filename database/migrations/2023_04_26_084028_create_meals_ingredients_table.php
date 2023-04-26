<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals_ingredients', function (Blueprint $table) {
            $table->integer('meal_id')->unsigned();
            $table->integer('ingredient_id')->unsigned();
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals_ingredients');
    }
}
