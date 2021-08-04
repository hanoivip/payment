<?php

namespace Hanoivip\Payment\Activities;

use Hanoivip\Events\Gate\UserTopup;
use Illuminate\Support\Facades\Log;

class RankingActivity
{
    public function handle(UserTopup $event)
    {
        Log::debug("Ranking ...");
    }
}