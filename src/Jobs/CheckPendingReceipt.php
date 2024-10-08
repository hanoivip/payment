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
use Hanoivip\Shop\Facades\OrderFacade;
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
                        $this->release(30);
                    else 
                        $this->release(60);
                }
                else if ($result->isFailure())
                {
                    //Log::debug(">> payment is invalid!");
                }
                else 
                {
                    $record = PaymentFacade::get($this->receipt);
                    $orderDetail = OrderFacade::detail($record->order);
                    //Log::error(print_r($orderDetail, true));
                    $ok = true;
                    switch ($this->delivery)
                    {
                        case 'game':
                            if (empty($orderDetail->cart->delivery_info))
                            {
                                Log::error("PaymentToGame flow, but target empty. Send result to web balance!");
                                $ok = $ok && BalanceFacade::add($this->userId, $result->getAmount(), "PaymentToGame1", 0, $result->getCurrency());
                            }
                            else
                            {
                                // check enough money
                                $amount = $result->getAmount();
                                $converted = BalanceFacade::convert($amount, $result->getCurrency(), $orderDetail->currency);
                                if ($converted >= $orderDetail->price) {
                                    //TODO: implement this fea
                                    //$r = GameHelper::rechargeByMoney($this->userId, $orderDetail->cart->delivery_info->svname, $result->getAmount(), $orderDetail->cart->delivery_info->roleid);
                                    $items = $orderDetail->cart->items;
                                    if (!empty($items))
                                    {
                                        foreach ($items as $item)
                                        {
                                            $r = GameHelper::recharge($this->userId, $orderDetail->cart->delivery_info->svname, $item->code, $orderDetail->cart->delivery_info->roleid);
                                            if (gettype($r) == 'boolean')
                                            {
                                                $ok = $ok && $r;
                                            }
                                            else
                                            {
                                                $ok = false;
                                            }
                                        }
                                    }
                                    if ($converted > $orderDetail->price) {
                                        $changes = $converted - $orderDetail->price;
                                        BalanceFacade::add($this->userId, $changes, "PaymentToGame3", 0, $orderDetail->currency);
                                    }
                                }
                                else
                                {
                                    // not enough money, send back web wallet
                                    $ok = $ok && BalanceFacade::add($this->userId, $result->getAmount(), "PaymentToGame2", 0, $result->getCurrency());
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
                        OrderFacade::onFinish($record->order, "PaymentOK");
                    }
                    else
                    {
                        // should retry
                        $this->release(30);
                    }
                }
            }
            else 
            {
                Log::error("CheckPendingReceipt query transaction $this->receipt error..retry after 10 min");
                $this->release(180);
            }
        }, function () {
            // Could not obtain lock...
            return $this->release(60);
        });
            
    }
}
