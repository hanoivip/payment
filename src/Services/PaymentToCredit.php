<?php

namespace Hanoivip\Payment\Services;

class PaymentToCredit implements IPaymentDone
{
    use DefPostProcess;
    
    protected $delivery = 'web';
    
}