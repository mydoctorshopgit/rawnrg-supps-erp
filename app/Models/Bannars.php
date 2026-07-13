<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bannars extends Model
{
    protected $table = 'bannars';

    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'banner_type',
        'sku',
        'product_title',
        'price',
        'vat',
        'button_text',
        'url',
    ];
}
