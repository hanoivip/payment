<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\Payment\Models\BalanceMod;
use Hanoivip\Events\Payment\BalanceRefunded;

class BalanceRefund extends Command
{
    protected $signature = 'balance:refund {fromTime} {toTime} {debug=1}';
    
    protected $description = 'Refund all player who buy game items during an interval time';
    
    public function handle()
    {
        $fromTime = $this->argument('fromTime');
        $toTime = $this->argument('toTime');
        $debug = $this->argument('debug');
        // main balance
        $logs = BalanceMod::where('created_at', '>=', $fromTime)
        ->where('created_at', '<', $toTime)
        ->where('balance_type', 0)
        ->where('balance', '<', 0)
        ->get();
        if ($logs->isNotEmpty())
        {
            foreach ($logs as $log)
            {
                $amount = -1 * $log->balance;
                if (!empty($debug))
                {
                    $this->info("Debug: Refund user $log->user_id amount $amount");
                }
                else
                {
                    $reason = "Coin refund from $fromTime to $toTime";
                    $result = BalanceFacade::add($log->user_id, $amount, $reason);
                    if ($result)
                    {
                        $this->info("Refund user $log->user_id amount $amount success");
                        event(new BalanceRefunded($log->user_id, $amount, $reason));
                    }
                    else 
                    {
                        $this->error("Refund user $log->user_id amount $amount fail");
                    }
                }
            }
        }
    }
}
