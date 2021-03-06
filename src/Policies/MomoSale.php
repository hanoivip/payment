<?php

namespace Hanoivip\Payment\Policies;

use Hanoivip\Payment\Contracts\ITopupPolicy;

class MomoSale implements ITopupPolicy
{
    protected $config;
    
    public function onTopup($type, $coin)
    {
        if ($type == 'MOMO')
            return [ 0 => intval(0.2 * $coin) ];
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