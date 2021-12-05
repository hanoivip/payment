<?php

namespace Hanoivip\Payment\Commands;

use Illuminate\Console\Command;

class StatNew extends Command
{
    protected $signature = 'stat:new {key} {startTime} {endTime}';
    
    protected $description = 'New stat key';
    

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
    }
}
