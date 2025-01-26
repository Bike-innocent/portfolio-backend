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
        Schema::create('template_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('version');
            $table->date('release_date');
            $table->date('last_updated_date')->nullable();
            $table->json('updates')->nullable(); // New column for version updates
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
        Schema::dropIfExists('template_versions');
    }
};
