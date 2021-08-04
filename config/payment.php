<?php

return [
    'in_process' => true,
    'remote' => [
        'uri' => 'http://payment-host.test/',
        'key' => '',
    ],
    'declare_wrong_cutoff' => 50,
    'enabled_types' => ['VTT', 'VNP'],
    'cutoffs' => ['VTT' => 'viettelCutoffs', 'VNP' => 15],
    'methods' => [
        'credit' => [ 'name' => __('hanoivip::payment.credit'), 'service' => 'CreditPaymentMethod', 'need_config' => false, 
            'enable' => true],// otp? captcha?
        'tsr1' => ['name' => __('hanoivip::payment.card.tsr'), 'service' => 'TsrPaymentMethod', 
            'need_config' => true, 
            'enable' => true, 
            'setting' => ['partner_id' => '0345167261', 'partner_secret' => '0aaf0fd0097a9c5e3b734b59104cffdd']],
        'momo1' => ['name' => __('hanoivip::payment.momo'), 'service' => 'MomoPaymentMethod', 'need_config' => false, 
            'enable' => true],
    ],
];