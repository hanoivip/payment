<?php

namespace Hanoivip\Payment\Policies;

use Hanoivip\Payment\Contracts\ITopupPolicy;

class GateCardSale implements ITopupPolicy
{
    protected $config;
    
    public function onTopup($type, $coin)
    {
        if ($type == 'GATE')
            return [ 0 => intval(0.15 * $coin) ];
    }

    public function info()
    {
        return $this->config;
    }
    
    public function setInfo($info)
    {
        $this->config = $info;
    }


    
}