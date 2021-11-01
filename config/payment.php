<?php

return [
    'in_process' => true,
    'remote' => [
        'uri' => 'http://payment-host.test/',
        'key' => '',
    ],
    'methods' => [
        'credit' => [ 'name' => __('hanoivip::payment.credit'), 'service' => 'CreditPaymentMethod', 'need_config' => false, 
            'enable' => true],// otp? captcha?
        'tsr1' => ['name' => __('hanoivip::payment.tsr'), 'service' => 'TsrPaymentMethod', 
            'need_config' => true, 
            'enable' => true, 
            'setting' => ['partner_id' => '0345167261', 'partner_secret' => '0aaf0fd0097a9c5e3b734b59104cffdd']],
        'momo1' => ['name' => __('hanoivip::payment.momo'), 'service' => 'MomoPaymentMethod', 'need_config' => false, 
            'enable' => true],
    ],
    'tsr' => [
        'telco' => [
            'VIETTEL' => [ 'available' => true, 'title' => 'Viettel Card', 'need_dvalue' => true,
                'supported_values' => [50000 => '50k', 100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k']],
            'VINAPHONE' => [ 'available' => true, 'title' => 'Vinaphone Card', 'need_dvalue' => true,
                'supported_values' => [20000 => '20k', 30000 => '30k', 50000 => '50k', 100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k']],
            'MOBIFONE' => [ 'available' => false, 'title' => 'Mobifone Card', 'need_dvalue' => true,
                'supported_values' => [50000 => '50k', 100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k']],
            'VNMOBI' => [ 'available' => false, 'title' => 'Vietnammobi Card', 'need_dvalue' => true,
                'supported_values' => [50000 => '50k', 100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k']],
            'GATE' => [ 'available' => true, 'title' => 'FPT GATE', 'need_dvalue' => true,
                'supported_values' => [10000 => '10k', 20000 => '20k', 30000 => '30k', 50000 => '50k',
                    100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k', 1000000 => '1000k']],
            'ZING' => [ 'available' => true, 'title' => 'Zing Card', 'need_dvalue' => true,
                'supported_values' => [10000 => '10k', 20000 => '20k', 30000 => '30k', 50000 => '50k',
                    100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k', 1000000 => '1000k']],
        ],
        'telco_static' => [
            'VIETTEL' => 'Thẻ Viettel',
            'VINAPHONE' => 'Thẻ Vinaphone',
            'MOBIFONE' => 'Thẻ Mobifone',
            'VNMOBI' => 'Thẻ Vietnammobi',
            'GATE' => 'Thẻ FPT GATE',
            'ZING' => 'Thẻ Vinagame',
        ],
        'values_static' => [50000 => '50k', 100000 => '100k', 200000 => '200k', 300000 => '300k', 500000 => '500k'],
    ],
    // web topup methods
    'webtopup' => [
        'methods' =>['tsr1']
    ]
];