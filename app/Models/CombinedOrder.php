<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CombinedOrder extends Model
{
    protected $fillable = ['user_id', 'combined_order_id', 'shipping_address', 'grand_total', 'guest_id', 'stripe_payment_intent_id', 'payment_status', 'payment_details'];
    public function orders(){
    	return $this->hasMany(Order::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
