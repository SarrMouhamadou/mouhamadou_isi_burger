<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'customer_name', 'customer_email', 'status', 'total'];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function burgers()
    {
        return $this->belongsToMany(Burger::class, 'order_burger')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    use HasFactory;
}
