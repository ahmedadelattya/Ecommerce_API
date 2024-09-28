<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationship to Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // Accessor for human-readable date
    public function getHumanReadableCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
    // Accessor for human-readable updated_at date
    public function getHumanReadableUpdatedAtAttribute()
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }
}
