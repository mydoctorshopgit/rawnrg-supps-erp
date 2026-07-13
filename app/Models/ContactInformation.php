<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
    use HasFactory;
    protected $table = 'customer_contact_information';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'customer_detail_id',
        'first_name', 
        'last_name', 
        'email',
        'office_number', 
        'mobile_number', 
     
    ];
    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class,'customer_detail_id');
    }
}
