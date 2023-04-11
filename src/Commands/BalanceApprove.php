<?php

namespace Hanoivip\Payment\Commands;

use Hanoivip\Payment\Services\BalanceRequest;
use Illuminate\Console\Command;

class BalanceApprove extends Command
{
    protected $signature = 'balance:approve {id}';
    
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
                $this->info("Approving id $pending->id");
                $result = $this->requests->approve($userId, $pending->id);
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
            $result = $this->requests->approve($userId, $id);
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
