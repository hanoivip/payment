<?php

namespace Hanoivip\Payment\Services;

class PaymentToGame implements IPaymentDone
{
    use DefPostProcess;
    
    protected $delivery = 'game';
    
}