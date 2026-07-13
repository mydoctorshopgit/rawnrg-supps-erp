<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'combined_order_id',
        'user_id',
        'customer_detail_id',
        'guest_id',
        'seller_id',
        'assign_delivery_boy',
        'shipping_address',
        'additional_info',
        'shipping_type',
        'delivery_name',
        'post_code',
        'order_from',
        'pickup_point_id',
        'carrier_id',
        'delivery_status',
        'payment_type',
        'manual_payment',
        'manual_payment_data',
        'payment_status',
        'payment_details',
        'grand_total',
        'total_tax',
        'coupon_discount',
        'code',
        'invoice_number',
        'tracking_code',
        'purchase_order_number',
        'notes',
        'carrier_name',
        'date',
        'is_pharmaceutical',
        'viewed',
        'delivery_viewed',
        'cancel_request',
        'cancel_request_at',
        'payment_status_viewed',
        'commission_calculated',
        'delivery_history_date',
        'status',
        'shipping_cost',
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
