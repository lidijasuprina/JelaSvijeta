<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('tag_id')->unsigned();
            $table->string('locale');
            $table->string('title');

            $table->unique(['tag_id', 'locale']);
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
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
        Schema::dropIfExists('tags_translations');
    }
}
