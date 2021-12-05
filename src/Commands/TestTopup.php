<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Events\Gate\UserTopup;

class TestTopup extends Command
{
    protected $signature = 'test:topup {uid} {amount}';
    
    protected $description = 'Test of topup';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
        $uid = $this->argument('uid');
        $amount = $this->argument('amount');
        event(new UserTopup($uid, 'VTT', $amount, "8888888"));
    }
}
