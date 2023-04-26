<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->unsigned();
            $table->string('locale');
            $table->string('title');

            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('categories_translations');
    }
}
