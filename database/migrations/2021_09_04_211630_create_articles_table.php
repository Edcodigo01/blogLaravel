<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title',120);
            $table->text('description',20000);
            $table->string('ideditor');
            $table->string('author',100);

            $table->date('date');
            $table->time('hour');
            $table->dateTime('datetime');
            $table->string('slug')->nullable();
            $table->enum('status',['publicado','no-publicado','eliminado'])->default('publicado');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');

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
        Schema::dropIfExists('articles');
    }
}
