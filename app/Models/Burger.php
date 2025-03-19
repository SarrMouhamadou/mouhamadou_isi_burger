<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Burger extends Model
{
    protected $fillable = ['name', 'price', 'image', 'description', 'stock', 'archived' ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_burger')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    public function isAvailable()
    {
        return $this->stock > 0 && !$this->archived;
    }
    use HasFactory;


}
