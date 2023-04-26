<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('meal_id')->unsigned();
            $table->string('locale');
            $table->string('title');
            $table->string('description');

            $table->unique(['meal_id', 'locale']);
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
            $table->foreign('locale')->references('code')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals_translations');
    }
}
