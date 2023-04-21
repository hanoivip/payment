<?php

namespace Hanoivip\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    function order_detail()
    {
        return $this->hasOne('Hanoivip\Iap\Models\Order', 'order', 'order');
    }
}
