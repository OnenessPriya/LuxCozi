<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function stores() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
     public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function orderProducts() {
        return $this->hasMany('App\Models\OrderProduct', 'order_id', 'id');
    }
}
