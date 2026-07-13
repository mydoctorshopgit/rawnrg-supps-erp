<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * Get the Zone that owns the Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeIsEnabled($query)
    {
        return $query->where('status', '1');
    }
    public function HeadOffice()
    {
        return $this->hasMany(HeadOffice::class);
    }
    public function creditDelivery()
    {
        return $this->hasMany(CreditDelivery::class);
    }
}
