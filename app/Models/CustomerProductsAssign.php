<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class CustomerProductsAssign extends Model
{
    
    protected $table = 'customers_products_assign';
    protected $fillable = [
        'customer_detail_id',
        'products_id',
        'product_code',
        'nhssc_npc',
        'brand_name',
        'pack_qty',
        'unit_price',
        'pack_price',
    ];
    public function products()
    {
        return $this->belongsTo(Product::class);
    }

     public function customer_detail()
    {
        return $this->belongsTo(CustomerDetail::class);
    }
}
