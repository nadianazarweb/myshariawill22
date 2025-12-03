<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('question_option_type_id');
            $table->unsignedBigInteger('option_id');
            $table->integer('sort_id')->default(0);
            $table->tinyInteger('is_required')->default(0);
            $table->string('incre_decre_data_for')->nullable();
            $table->string('incre_decre_get_data')->nullable();
            $table->timestamps();
            
            $table->foreign('question_option_type_id')->references('id')->on('question_option_types');
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_questions');
    }
}
