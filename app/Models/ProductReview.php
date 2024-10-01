<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'user_id', 'rating', 'comment'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     // Accessor for human-readable date
     public function getHumanReadableCreatedAtAttribute()
     {
         return Carbon::parse($this->created_at)->diffForHumans();
     }
}
