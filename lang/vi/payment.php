<?php

return [
    'methods' => [
        'next' => 'Chọn',
        'title' => 'Chọn phương thức thanh toán',
        'empty' => 'Hiện chưa có cách nào để thanh toán, vui lòng quay lại sau!'
    ],
    'credit' => [
        'no-point' => 'Chưa có tài khoản nào',
    ],
    'CreditPaymentMethod' => [
        'guidelines' => 'You can pay with credit in your web account. Credit can be got in many way. For more detail, click link below.',
        'url' => env('APP_URL') . '/blog/pay-with-credit-guidelines',
    ],
    'TsrPaymentMethod' => [
        'guidelines' => 'You can pay with Prepaid Vietnamese Card (game & telco cards). We currently support Zing, Vinaphone & VTC cards. For more detail, click link below',
        'url' =>  env('APP_URL') . '/blog/pay-with-tsr-guidelines',
    ],
    'PaytrPaymentMethod' => [
        'guidelines' => 'You can pay with VISA/Master Card via paytr.com',
        'url' =>  env('APP_URL') . '/blog/pay-with-paytr-guidelines',
    ]
];