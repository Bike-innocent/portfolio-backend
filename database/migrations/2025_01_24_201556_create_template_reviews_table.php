<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('template_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('reviewer_name');
            $table->integer('rating');
            $table->text('review_text')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_reviews');
    }
};
