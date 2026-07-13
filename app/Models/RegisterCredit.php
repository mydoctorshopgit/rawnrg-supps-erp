<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterCredit extends Model
{
    protected $table = 'customer_register_credit';
    protected $fillable = [
        'user_id',
        'organization_type',
        'company_name',
        'department_name',
        'statement_email',
        'phone_number',
        'mobile_number',
        'organization_name',
        'is_completed',
        "bussiness_name",
    ];
    use HasFactory;

    public function creditDelivery()
    {
        return $this->hasMany(CreditDelivery::class, 'credit_id', 'id');
    }
//     public function creditDelivery()
// {
//     return $this->hasMany(CreditDelivery::class, 'register_credit_id');
// }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
