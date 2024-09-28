<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * This will prevent mass assignment on all fields unless explicitly allowed.
     */
    protected $guarded = [];

    /**
     * Example of defining relationships, scopes, or accessors can go here.
     * You can add custom methods to handle relationships or additional logic.
     */
}
