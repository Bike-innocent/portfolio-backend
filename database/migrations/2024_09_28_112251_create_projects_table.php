<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Name of the project (not nullable)
            $table->text('description');
            $table->string('image');  // This will store the image path
            $table->string('client')->nullable();  // Client name (nullable)
            $table->string('tools')->nullable();  // Tools used (nullable)
            $table->string('start_date')->nullable();  // Start date of the project (nullable)
            $table->string('end_date')->nullable();  // End date of the project (nullable)
            $table->string('category')->nullable();  // Category of the project (nullable)
            $table->string('url')->nullable();  // URL for live project view (nullable)
            $table->timestamps();  // This will handle created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
