<?php

return [
    'methods' => [
        'next' => 'Next',
        'title' => 'Please choose payment method',
        'empty' => 'Still have no payment method. Plz try later.'
    ],
    'credit' => [
        'no-point' => 'You have 0 point',
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
    ],
    'PaypalPaymentMethod' => [
        'guidelines' => 'You can pay with VISA/Master Card via Paypal',
        'url' =>  env('APP_URL') . '/blog/pay-with-paypal-guidelines',
    ]
];