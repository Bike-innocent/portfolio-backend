<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * All attributes are mass assignable unless explicitly listed in the $guarded array.
     */
    protected $guarded = [];

    /**
     * Automatically boot and generate a slug for the blog post.
     */
    public static function boot()
    {
        parent::boot();

        // Automatically generate a slug when creating a new blog post
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = self::generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug of random text for the blog post.
     * This slug can be used in URLs.
     *
     * @return string
     */
    public static function generateSlug()
    {
        // Generates a random 8-character slug
        return Str::random(8);
    }

    // Additional methods or relationships can be added here
}
