<?php

namespace Hanoivip\Payment\Contracts;

interface ITopupPolicy
{
    public function info();
    
    public function setInfo($info);
    
    /**
     * @param number $type
     * @param number $coin
     * @return array coin type => coin number
     */
    public function onTopup($type, $coin);
}