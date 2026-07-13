<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingTerms extends Model
{
    use HasFactory;
    protected $table = 'customer_shipping_terms';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'customer_detail_id',
        'order_value',
        'delivary_charges',
        'international_shipping_term',
       
    ];
    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class,'customer_detail_id');
    }
}
