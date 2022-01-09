<?php

namespace Hanoivip\Payment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\PaymentContract\Facades\PaymentFacade;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\Events\Gate\UserTopup;

class CheckPendingReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 30;
    
    private $userId;
    
    private $receipt;
    
    public function __construct($userId, $receipt)
    {
        $this->userId = $userId;
        $this->receipt = $receipt;
    }
    
    public function handle()
    {
        Redis::funnel('CheckPendingReceipt-payment@' . $this->userId)->limit(1)->then(function () {
            Log::debug("CheckPendingReceipt at payment $this->userId $this->receipt");
            $result = PaymentFacade::query($this->receipt);
            if ($result instanceof IPaymentResult)
            {
                if ($result->isPending())
                {
                    $this->release(180);
                }
                else if ($result->isFailure())
                {
                    
                }
                else 
                {
                    event(new UserTopup($this->userId, 0, $result->getAmount(), $this->receipt));
                    BalanceFacade::add($this->userId, $result->getAmount(), "WebTopup:" . $this->receipt);
                }
            }
            else 
            {
                Log::error("CheckPendingReceipt query transaction $this->receipt error..retry after 10 min");
                $this->release(600);
            }
        }, function () {
            // Could not obtain lock...
            return $this->release(120);
        });
            
    }
}