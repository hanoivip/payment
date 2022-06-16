<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\User\Facades\UserFacade;
use Hanoivip\Payment\Notifications\BalanceChanged;

class TestBalance extends Command
{
    protected $signature = 'admin:balance {uid} {balance} {reason}';
    
    protected $description = 'Admin add balance';
    
    public function handle()
    {
        $uid = $this->argument('uid');
        $balance = $this->argument('balance');
        $reason = $this->argument('reason');
        if (BalanceFacade::add($uid, $balance, "Admin added coin for you"))
        {
            $user = UserFacade::getUserCredentials($uid);
            if (!empty($user))
            {
                $user->notify(new BalanceChanged($balance, $reason));
            }
            $this->info("Add balance success");
        }
        else 
            $this->error("Add balance failed!");
    }
}
