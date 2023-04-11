<?php

namespace Hanoivip\Payment\Services;

use Hanoivip\Payment\Models\BalanceRequest as RequestLog;

/**
 *
 * @author GameOH
 * 22/02/2023: Add currency supports
 *
 */
class BalanceRequest
{
    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    
    private $balance;
    
    public function __construct(BalanceService $balance)
    {
        $this->balance = $balance;
    }
    
    public function request($userId, $targetId, $reason, $amount, $currency = "")
    {
        $log = new RequestLog();
        $log->gm_id = $userId;
        $log->target_id = $targetId;
        $log->reason = $reason;
        $log->amount = $amount;
        $log->currency = $currency;
        $log->status = self::PENDING;
        $log->save();
        return $log;
    }
    
    public function pendings()
    {
        return RequestLog::where('status', self::PENDING)->get();
    }
    
    public function approve($userId, $requestId)
    {
        $log = RequestLog::find($requestId);
        if (empty($log))
        {
            return __('hanoivip.payment::request.not-found');
        }
        if ($log->status != self::PENDING)
        {
            return __('hanoivip.payment::request.determined');
        }
        $log->approve_id = $userId;
        $log->status = self::APPROVED;
        $log->save();
        // Actual modify balance
        return $this->balance->add($log->target_id, $log->amount, $log->reason, $log->balance_type, $log->currency);
    }
    
    public function reject($userId, $requestId)
    {
        $log = RequestLog::find($requestId);
        if (empty($log))
        {
            return __('hanoivip.payment::request.not-found');
        }
        if ($log->status != self::PENDING)
        {
            return __('hanoivip.payment::request.determined');
        }
        $log->approve_id = $userId;
        $log->status = self::REJECTED;
        $log->save();
        return $log;
    }
}