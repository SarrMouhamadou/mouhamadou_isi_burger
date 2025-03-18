<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Burger extends Model
{
    protected $fillable = ['name', 'price', 'image', 'description', 'stock' ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
    use HasFactory;
}
