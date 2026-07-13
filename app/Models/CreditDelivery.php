<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditDelivery extends Model
{
    protected $table = 'credit_delivery_address';
    use HasFactory;
    protected $fillable = [
        'credit_id',
        'post_code',
        'address1',
        'address2',
        'address3',
        'town',
        'city',
        'county',
        'country',
    ];
    public function credit()
    {
        return $this->belongsTo(RegisterCredit::class,'credit_id');
    }
    public function countries()
    {
        return $this->belongsTo(Country::class,'country','id');
    }
      public function user()
    {
        return $this->belongsTo(User::class, 'credit_id', 'id');
    }
}
