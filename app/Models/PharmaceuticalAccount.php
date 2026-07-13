<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmaceuticalAccount extends Model
{
    protected $table = 'pharmaceutical_customer';

    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'holder_first_name',
        'holder_last_name',
        'holder_email',
        'account_number',
        'license_type',
        'license_name',
        'license_number',
        'registration_date',
        'signature',
        'is_pharmaceutical',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
