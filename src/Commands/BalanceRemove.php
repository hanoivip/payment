<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\User\Facades\UserFacade;

class BalanceRemove extends Command
{
    protected $signature = 'balance:remove {uid} {balance}';
    
    protected $description = 'Remove balance';
    
    public function handle()
    {
        $uidOrUsername = $this->argument('uid');
        $balance = $this->argument('balance');
        $user = UserFacade::getUserCredentials($uidOrUsername);
        if (!empty($user))
        {
            BalanceFacade::remove($user->id, $balance, "admin-command");
            $this->info("ok");
        }
        else
        {
            $this->error("user not found");
        }
        
    }
}
