<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function notFoundMessage(): string
    {
        return "Sipariş bulunamadı";
    }

    /**
     * Siparişe ait ilgili müşteri modelini döner
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|\Jenssegers\Mongodb\Relations\HasOne
     */
    public function customerInfo()
    {
        return $this->hasOne(User::class, '_id', 'customer');
    }
}
