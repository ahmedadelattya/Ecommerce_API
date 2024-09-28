<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tags');
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
