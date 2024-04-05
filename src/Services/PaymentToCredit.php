<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\PaymentMethodContract\IPaymentDone;

class PaymentToCredit implements IPaymentDone
{
    use DefPostProcess;
    
    protected $delivery = 'web';
    
}