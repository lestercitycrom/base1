<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    public $timestamps = false;
    protected $fillable = ['customer_name', 'product_name', 'quantity', 'price', 'created_at'];
}