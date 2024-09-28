<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'total', 'item_count'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
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
     // Accessor for human-readable updated_at date
     public function getHumanReadableUpdatedAtAttribute()
     {
         return Carbon::parse($this->updated_at)->diffForHumans();
     }
}
