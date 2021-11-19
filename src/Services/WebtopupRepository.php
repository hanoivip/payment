<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\Payment\Models\Transaction;
use Illuminate\Support\Str;
use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\Payment\Models\WebtopupLogs;
use Hanoivip\PaymentMethodTsr\TsrTransaction;
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
    }
    
    public function list($userId, $page = 0, $count = 10)
    {
        $logs = WebtopupLogs::where('user_id', $userId)
        ->skip($page * $count)
        ->take($count)
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
            $submissions = TsrTransaction::whereIn('trans', $arr)->get();
            $objects = [];
            if ($submissions->isNotEmpty())
            {
                foreach ($submissions as $sub)
                {
                    $obj = new \stdClass();
                    $obj->password = $sub->password;
                    $obj->status = $this->getSubmissionStatus($sub);
                    $obj->dvalue = $sub->dvalue;
                    $obj->value = $sub->value;
                    $obj->penalty = $obj->status == 3 ? '50' : '0';
                    $obj->mapping = $sub->trans;
                    //$obj->delay = $sub->delay;
                    //$obj->success = $sub->success;
                    $obj->time = $times[$sub->trans];
                    $objects[] = $obj;
                }
                $total = WebtopupLogs::where('user_id', $userId)->count();
                return [$objects, ceil($total / 10), $page];
            }
        }
    }
    
    private function getSubmissionStatus($submission)
    {
        $result = $this->service->query($submission->trans);
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
                if ($submission->value != $submission->dvalue)
                {
                    $status = 3;
                }
            }
        }
        return $status;
    }
}