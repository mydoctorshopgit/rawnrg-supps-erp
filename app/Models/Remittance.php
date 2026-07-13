<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remittance extends Model
{
    protected $table = 'remittance';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'order_id',
        'customer_detail_id',
        'payment_date',
        'payment_ref',
        'add_remittance_value',
        'PDF',
       
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'customer_detail_id','id');
    }
}
