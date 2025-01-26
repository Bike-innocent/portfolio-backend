<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

   

    public static function generateSlug()
    {
        do {
            $slug = Str::random(8); // Generate an 8-character random string
        } while (self::where('slug', $slug)->exists());

        return $slug;
    }


    /**
     * Define the relationship with the TemplateReview model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(TemplateReview::class);
    }

    /**
     * Define the relationship with the TemplateVersion model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
        return $this->hasMany(TemplateVersion::class);
    }
}
