<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\PaymentMethodContract\IPaymentResult;

class SavedPaymentResult implements IPaymentResult
{
    private $transId;
    private $result;
    public function __construct($transId, $str)
    {
        $this->transId = $transId;
        $this->result = json_decode($str, true);
    }
    
    public function getDetail()
    {
        return $this->result['detail'];
    }

    public function toArray()
    {
        return $this->result;
    }

    public function isPending()
    {
        return $this->result['isPending'];
    }

    public function isFailure()
    {
        return $this->result['isFailure'];
    }

    public function getTransId()
    {
        return $this->transId;
    }

    public function isSuccess()
    {
        return $this->result['isSuccess'];
    }

    public function getAmount()
    {
        return $this->result['amount'];
    }
    
    public function getCurrency()
    {
        return $this->result['currency'];
    }

    
}