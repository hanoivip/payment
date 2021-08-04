<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;

class TestBalance extends Command
{
    protected $signature = 'test:balance {uid} {balance}';
    
    protected $description = 'Add/remove balance';
    
    public function handle()
    {
        $uid = $this->argument('uid');
        $balance = $this->argument('balance');
        BalanceFacade::add($uid, $balance, "Test from command");
    }
}
