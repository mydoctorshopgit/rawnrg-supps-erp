<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterOffice extends Model
{
    use HasFactory;
    protected $table = 'customer_register_office';
    protected $fillable = [
        'customer_detail_id',
        'postcode',
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
