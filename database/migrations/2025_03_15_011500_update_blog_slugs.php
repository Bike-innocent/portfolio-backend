<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Blog;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Loop through existing blogs and update their slugs
        Blog::all()->each(function ($blog) {
            $newSlug = Blog::generateSlug($blog->title);
            $blog->slug = $newSlug;
            $blog->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // There's no rollback action since old slugs were random
    }
};