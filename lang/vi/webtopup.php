<?php

return [
    'history' => [
        'status' => [
            0 => 'Đang đợi xử lý',
            1 => 'Đang đợi thanh toán',
            2 => 'Thanh toán thất bại',
            3 => 'Thanh toán xong',
            4 => 'Thanh toán xong(còn thừa tiền)',
            5 => 'Thanh toán chưa đủ'
        ],
        'game_status' => [
            0 => 'Đang đợi xử lý',
            1 => 'Chuyển thành công',
            2 => 'Đang thử lại'
        ],
        'empty' => 'Chưa thanh toán lần nào'
    ],
    'no-method' => 'Không có cách nào để nạp. Mời liên hệ hỗ trợ viên.',
    'callback-error' => 'Xử lý thanh toán gặp lỗi, mời liên hệ hỗ trợ viên. (callback-error)',
    'query-error' => 'Truy vấn giao dịch gặp lỗi, mời thử lại trước khi liên hệ hỗ trợ viên',
    'pending' => 'Giao dịch cần thêm chút thời gian để hoàn thành..đợi thêm vài ..',
    'success' => 'Nạp xu web thành công',
    'callback-in-progress' => 'Đang xử lý rồi, hãy đợi chút..',
    'error-occured' => 'Lỗi xẩy ra. Mời thử lại! (ex)',
    'log-fail' => 'Lỗi xẩy ra. Mời thử lại! (log )',
];