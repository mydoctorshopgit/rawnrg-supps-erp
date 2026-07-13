<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;
    protected $table = 'customer_delivery_address';
    protected $fillable = [
        'customer_detail_id',
        'postcode',
        'delivery_name',
        'address1',
        'address2',
        'address3',
        'town',
        'city',
        'county',
        'country',
    ];
    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class,'customer_detail_id');
    }
       public function countries()
    {
        return $this->belongsTo(Country::class,'country','id');
    }
}
