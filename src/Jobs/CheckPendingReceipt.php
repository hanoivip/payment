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
use Hanoivip\Game\Facades\GameHelper;

class CheckPendingReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // 20mins fast check + 500mins slow check
    public $tries = 60;
    
    private $userId;
    
    private $receipt;
    
    private $delivery;
    
    public function __construct($userId, $receipt, $delivery)
    {
        $this->userId = $userId;
        $this->receipt = $receipt;
        $this->delivery = $delivery;
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
                    if ($this->attempts() < 10)
                        $this->release(60);
                    else 
                        $this->release(120);
                }
                else if ($result->isFailure())
                {
                    //Log::debug(">> payment is invalid!");
                }
                else 
                {
                    $ok = true;
                    switch ($this->delivery)
                    {
                        case 'game':
                            $target = GameHelper::getUserDefaultRole($this->userId);
                            if (empty($target))
                            {
                                Log::error("PaymentToGame flow, but target empty. Send card to coin!");
                                $ok = $ok && BalanceFacade::add($this->userId, $result->getAmount(), "PaymentToGame", 0, $result->getCurrency());
                            }
                            else
                            {
                                $r = GameHelper::rechargeByMoney($this->userId, $target->server, $result->getAmount(), $target->role);
                                if (gettype($r) == 'boolean')
                                {
                                    $ok = $ok && $r;
                                }
                                else
                                {
                                    $ok = false;
                                }
                            }
                            break;
                        case 'order':
                            // get order from transaction and notify order get paid!
                            break;
                        case 'web':
                        default:
                            $ok = $ok && BalanceFacade::add($this->userId, $result->getAmount(), "PaymentToCredit", 0, $result->getCurrency());
                            break;
                    }
                    if ($ok)
                    {
                        event(new UserTopup($this->userId, 0, $result->getAmount(), $this->receipt));
                    }
                    else
                    {
                        // should retry
                        $this->release(60);
                    }
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
