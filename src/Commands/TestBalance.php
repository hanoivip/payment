<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\User\Facades\UserFacade;

class TestBalance extends Command
{
    protected $signature = 'test:balance {uid} {balance}';
    
    protected $description = 'Add/remove balance';
    
    public function handle()
    {
        $uidOrUsername = $this->argument('uid');
        $balance = $this->argument('balance');
        $user = UserFacade::getUserCredentials($uidOrUsername);
        if (!empty($user))
        {
            BalanceFacade::add($user->id, $balance, "Test balance command");
            $this->info("ok");
        }
        else
        {
            $this->error("user not found");
        }
        
    }
}
