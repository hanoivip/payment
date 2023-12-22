<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\Payment\Models\Transaction;
use Illuminate\Support\Str;
use Hanoivip\PaymentMethodContract\IPaymentResult;

// TransactionRepository
class TransactionService
{
    public function newTrans()
    {
        $record = new Transaction();
        $record->trans_id = Str::random(8);
        $record->save();
        return $record;
    }
    
    public function get($transId)
    {
        return Transaction::where('trans_id', $transId)->first();
    }
    /**
     * 
     * @param Transaction $record
     * @param IPaymentResult $result
     */
    public function saveResult($record, $result)
    {
        $record->result = json_encode($result->toArray());
        $record->save();
    }
    
    public function list($page = 0, $count = 10)
    {
        
    }
}