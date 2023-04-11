<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\Payment\Services\BalanceRequest;
use Hanoivip\User\Facades\UserFacade;

class BalanceReject extends Command
{
    protected $signature = 'balance:reject {id}';
    
    protected $description = 'Approve a balance request';
    
    private $requests;
    
    public function __construct(BalanceRequest $requests)
    {
        parent::__construct();
        $this->requests = $requests;
    }
    
    public function handle()
    {
        $id = $this->argument('id');
        $userId = 0;//system
        if ($id === 'all')
        {
            $pendings =$this->requests->pendings();
            foreach ($pendings as $pending)
            {
                $this->info("Rejecting id $pending->id");
                $result = $this->requests->reject($userId, $pending->id);
                if (gettype($result) == 'string')
                {
                    $this->error($result);
                }
                else if ($result === false)
                {
                    $this->error('fail');
                }
                else
                {
                    $this->info("ok");
                }
            }
        }
        else
        {
            $result = $this->requests->reject($userId, $id);
            if (gettype($result) == 'string')
            {
                $this->error($result);
            }
            else if ($result === false)
            {
                $this->error('fail');
            }
            else
            {
                $this->info("ok");
            }
        }
    }
}
