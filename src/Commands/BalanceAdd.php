<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\User\Facades\UserFacade;
use Hanoivip\Payment\Notifications\BalanceChanged;

class BalanceAdd extends Command
{
    protected $signature = 'balance:add {uid} {balance} {reason}';
    
    protected $description = 'Add balance. No need to approve.';
    
    public function handle()
    {
        $uidOrUsername = $this->argument('uid');
        $balance = $this->argument('balance');
        $reason = $this->argument('reason');
        $user = UserFacade::getUserCredentials($uidOrUsername);
        if (!empty($user))
        {
            $result = BalanceFacade::add($user->id, $balance, $reason);
            if ($result)
            {
                $user->notify(new BalanceChanged($balance, $reason));
                $this->info("ok");
            }
            else
            {
                $this->error("Modify balance fail. Retry");
            }
        }
        else
        {
            $this->error("user not found");
        }
        
    }
}
