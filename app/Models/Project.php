<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    // Allow mass assignment on all fields
    protected $guarded = [];

    /**
     * Automatically generate a slug when a project is being created.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            // If no slug is set, create a random 8-character slug
            if (empty($project->slug)) {
                $project->slug = Str::random(8);
            }
        });
    }
}
