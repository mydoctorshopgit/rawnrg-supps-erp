<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    use HasFactory;
    protected $table = 'customer_details';
    protected $fillable = [
        'company_type',
        'account_type',
        'business_structure',
        'currency',
        'vat_rate',
        'status',
    ];
    public function headOffice()
    {
        return $this->hasMany(HeadOffice::class);
    }
    public function registerOffice()
    {
        return $this->hasMany(RegisterOffice::class);
    }
    public function deliveryAddress()
    {
        return $this->hasMany(DeliveryAddress::class);
    }
    public function contactInformation()
    {
        return $this->hasOne(ContactInformation::class);
    }
    public function shippingTerms()
    {
        return $this->hasMany(ShippingTerms::class);
    }
    public function accountPayable()
    {
        return $this->hasMany(AccountPayable::class,'customer_detail_id','id');
    }
    public function cusotmer_product_assign()
    {
        return $this->hasMany(CustomerProductsAssign::class);
    }

    public function remittance()
    {
        return $this->hasMany(Remittance::class);
    }
    public function account()
    {
        return $this->hasMany(Accounts::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
