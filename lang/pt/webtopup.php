<?php

return [
    'history' => [
        'status' => [
            0 => 'Init',
            1 => 'Unpaid',
            2 => 'Pay Fail',
            3 => 'Pay success',
            4 => 'Paid(changes)',
            5 => 'Insufficient paid'
        ],
        'game_status' => [
            0 => 'Wait payment',
            1 => 'Success',
            2 => 'Retry'
        ],
        'empty' => 'Have no payment'
    ],
    'no-method' => 'Have no way to pay. Contact CS!',
    'callback-error' => 'Payment error. Contact CS! (callback-error)',
    'query-error' => 'Query error. Try again!',
    'pending' => 'Transaction is more time to complete. Wait a minute..',
    'success' => 'Web topup success',
    'callback-in-progress' => 'Almost done.. plz kindly wait..',
    'error-occured' => 'System error. Try again. (ex)',
    'log-fail' => 'System error. Try again. (log)',
];