<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    use HasFactory;
    protected $table = 'customer_account_payable';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'customer_detail_id',
        'first_name', 
        'last_name', 
        'email',
        'office_number', 
        'mobile_number', 
        'confirmation_email', 
        'statement_email', 
        'post_code', 
        'address1', 
        'address2',
        'town',
        'city',
        'country',
        'account_name', 
        'bank_name', 
        'short_code', 
        'account_number', 
        'iban', 
        'swift_code',
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
