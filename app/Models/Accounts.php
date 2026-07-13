<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;
    protected $table = 'accounts';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'order_id',
        'customer_detail_id',
        'credit', 
        'debit', 
        'status',
        'dua_amount',
        'dua_date'
       
    ];
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class,'customer_detail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_detail_id', 'id');
    }
}
