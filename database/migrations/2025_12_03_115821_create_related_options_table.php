<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('related_question_id');
            $table->string('title');
            $table->integer('sort_id')->default(0);
            $table->timestamps();
            
            $table->foreign('related_question_id')->references('id')->on('related_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_options');
    }
}
