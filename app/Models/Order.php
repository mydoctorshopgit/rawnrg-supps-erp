<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'payment_status',
        'shipping_address',
        "shipping_cost",
        'order_from',
        'customer_detail_id',
        'combined_order_id',
        'user_id',
        'code',
        'date',
        'payment_type',
        'invoice_number',
        'assign_delivery_boy',
        'status',
        'grand_total',
        "payment_details",
        'delivery_charge',
        'wallet_amount',
        'seller_id',
        'pickup_point_id',
        'carrier_id',
        'total_tax',
        'guest_id',
    ];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id', 'seller_id');
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function club_point()
    {
        return $this->hasMany(ClubPoint::class);
    }

    public function delivery_boy()
    {
        return $this->belongsTo(User::class, 'assign_delivery_boy', 'id');
    }

    public function proxy_cart_reference_id()
    {
        return $this->hasMany(ProxyPayment::class)->select('reference_id');
    }
    public function account()
    {
        return $this->hasMany(Accounts::class);
    }
    public function customer_details()
    {
        return $this->belongsTo(CustomerDetail::class,'customer_detail_id','id');
    }
    
        public function customer()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
        public function combined_order()
    {
        return $this->belongsTo(CombinedOrder::class);
    }
}
