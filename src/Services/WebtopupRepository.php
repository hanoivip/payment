<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\PaymentMethodTsr\TsrTransaction;
use Hanoivip\Payment\Models\WebtopupLogs;
use Illuminate\Support\Facades\Log;

class WebtopupRepository
{
    private $service;
    
    public function __construct(
        NewTopupService $service)
    {
        $this->service = $service;
    }
    
    public function saveLog($userId, $transId)
    {
        $log = new WebtopupLogs();
        $log->user_id = $userId;
        $log->trans_id = $transId;
        $log->save();
        return true;
    }
    // 
    public function list($userId, $page = 0, $count = 10)
    {
        $logs = WebtopupLogs::where('user_id', $userId)
        //->skip($page * $count)
        //->take($count)
        ->orderBy('id', 'desc')
        ->get();
        if ($logs->isNotEmpty())
        {
            $arr = [];
            $times = [];
            foreach ($logs as $log)
            {
                $arr[] = $log->trans_id;
                $times[$log->trans_id] = $log->created_at;
            }
            // No way!
            $submissions = TsrTransaction::whereIn('trans', $arr)
            ->orderBy('id', 'desc')
            ->get();
            $objects = [];
            if ($submissions->isNotEmpty())
            {
                foreach ($submissions as $sub)
                {
                    /** @var \Hanoivip\PaymentMethodContract\IPaymentResult $result */
                    $result = $this->service->query($sub->trans);
                    $obj = new \stdClass();
                    $obj->serial = $sub->serial;
                    $obj->password = $sub->password;
                    $obj->status = $this->getSubmissionStatus($result, $sub);
                    $obj->dvalue = $sub->dvalue;
                    $obj->value = $result->getAmount(); //$sub->value;
                    $obj->penalty = $obj->status == 3 ? '50' : '0';
                    $obj->mapping = $sub->trans;
                    $obj->time = $times[$sub->trans];
                    $obj->trans = $sub->trans;
                    if (!empty($obj->password))
                        $objects[] = $obj;
                }
                $total = count($objects);
                //$total = WebtopupLogs::where('user_id', $userId)
                //->whereNotNull('password')
                //->count();
                return [array_slice($objects, $page * 10, 10), floor($total / 10), $page];
            }
        }
    }
    /**
     * 
     * @param IPaymentResult $result
     * @param TsrTransaction $record
     * @return number
     */
    private function getSubmissionStatus($result, $record)
    {
        $status = 0;
        if (gettype($result) == 'string')
        {
            $status = 1;
        }
        else
        {
            if ($result->isPending())
            {
                $status = 2;
            }
            else if ($result->isFailure())
            {
                $status = 1;
            }
            else 
            {
                if ($result->getAmount() != $record->dvalue)
                {
                    $status = 3;
                }
            }
        }
        return $status;
    }
}