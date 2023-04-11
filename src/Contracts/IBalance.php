<?php

namespace Hanoivip\Payment\Contracts;

interface IBalance
{
    /**
     * Lấy thông tin các loại tài khoản.
     *
     * @param number $uid
     * @return array Array of balance records
     */
    public function getInfo($uid);
    
    /**
     * Kiểm tra xem có đủ xu trong tài khoản không
     *
     * @param number $uid
     * @param number $coin
     * @param number $type
     * @return boolean
     */
    public function enough($uid, $amount, $type = 0, $currency = null);
    
    public function add($uid, $value, $reason, $type = 0, $currency = null);
    
    public function remove($uid, $value, $reason, $type = 0, $currency = null);
    
    public function getHistory($uid, $page = 1, $count = 10);
}