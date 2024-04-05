<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\PaymentMethodContract\IPaymentDone;

class PaymentToGame implements IPaymentDone
{
    use DefPostProcess;
    
    protected $delivery = 'game';
    
}