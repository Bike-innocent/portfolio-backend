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
    Schema::create('templates', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug', 10)->unique();
        $table->string('image');
        $table->text('description');
        $table->decimal('price', 8, 2);
        $table->string('live_link');
        $table->integer('downloads')->default(0);
        $table->string('category');
        $table->string('technologies')->nullable(); 
        $table->string('license')->default('Standard');
        $table->boolean('status')->default(false);
        $table->string('file_path')->nullable(); // ✅ Added to store zip file path
        $table->timestamps();
        $table->softDeletes();
    });
    

}


    /**
     * Reverse the migrations.
     */
   
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};


























