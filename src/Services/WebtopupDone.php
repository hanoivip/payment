<?php

namespace Hanoivip\Payment\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\Payment\Jobs\CheckPendingReceipt;

trait WebtopupDone 
{
    /**
     * 
     * @param IPaymentResult $result
     */
    function onTopupDone($userId, $receipt, $result)
    {
        try
        {
            if (gettype($result) == 'string')
            {
                return view('hanoivip::webtopup-failure', ['message' => $result]);
            }
            else
            {
                if ($result->isPending())
                {
                    dispatch(new CheckPendingReceipt($userId, $receipt))->delay(60);
                    return view('hanoivip::webtopup-pending', ['trans' => $receipt]);
                }
                else if ($result->isFailure())
                {
                    return view('hanoivip::webtopup-failure', ['message' => $result->getDetail()]);
                }
                else
                {
                    dispatch(new CheckPendingReceipt($userId, $receipt));
                    return view('hanoivip::webtopup-success');
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error("WebTopup payment callback exception:" . $ex->getMessage());
            dispatch(new CheckPendingReceipt($userId, $receipt))->delay(60);
            return view('hanoivip::webtopup-failure', ['message' => 'We are trying our best to finish your payment.']);
        }
    }
}