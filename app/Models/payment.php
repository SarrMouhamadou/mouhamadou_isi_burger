<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    protected $fillable = ['order_id', 'amount', 'payment_date'];

    public function order()
    {
        return $this->belongTo(Order::class);
    }
    use HasFactory;
}
