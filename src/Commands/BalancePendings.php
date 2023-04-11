<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\User\Facades\UserFacade;
use Hanoivip\Payment\Services\BalanceRequest;

class BalancePendings extends Command
{
    protected $signature = 'balance:pendings';
    
    protected $description = 'Get all balance request pendings';
    
    private $requests;
    
    public function __construct(BalanceRequest $requests)
    {
        parent::__construct();
        $this->requests = $requests;
    }
    
    public function handle()
    {
        $records = $this->requests->pendings();
        foreach ($records as $record)
        {
            $this->info("ID [$record->id] GM [$record->gm_id] request for [$record->target_id] with reason [$record->reason] amount [$record->amount]");
        }
        $this->info("that's all");
    }
}
