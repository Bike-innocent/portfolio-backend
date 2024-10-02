<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        // Automatically generate a random slug when creating a new blog post
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = self::generateSlug();
            }
        });
    }

    /**
     * Generate a unique 8-character slug.
     *
     * @return string
     */
    public static function generateSlug()
    {
        $slug = Str::random(8);

        // Ensure the slug is unique by checking the database
        while (self::where('slug', $slug)->exists()) {
            $slug = Str::random(8);
        }

        return $slug;
    }
}
