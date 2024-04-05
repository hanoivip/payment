<?php

namespace Hanoivip\Payment\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\Payment\Jobs\CheckPendingReceipt;
use Illuminate\View\View;
/**
 * Default payment post process. Add to web balance.
 * @author GameOH
 *
 */
trait DefPostProcess 
{
    /**
     * 
     * @param IPaymentResult $result
     * @return View
     */
    function onTopupDone($userId, $receipt, $result)
    {
        $expectJson = request()->expectsJson();
        try
        {
            if (gettype($result) == 'string')
            {
                if ($expectJson)
                {
                    return ['error' => 1, 'message' => $result, 'data' => ['detail' => $result]];
                }
                return view('hanoivip::webtopup-failure', ['message' => $result]);
            }
            else
            {
                if ($result->isPending())
                {
                    dispatch(new CheckPendingReceipt($userId, $receipt, $this->delivery))->delay(60);
                    if ($expectJson)
                    {
                        return ['error' => 0, 'message' => 'pending transaction', 'data' => $result->toArray()];
                    }
                    return view('hanoivip::webtopup-pending', ['trans' => $receipt]);
                }
                else if ($result->isFailure())
                {
                    if ($expectJson)
                    {
                        return ['error' => 2, 'message' => $result->getDetail(), 'data' => $result->toArray()];
                    }
                    return view('hanoivip::webtopup-failure', ['message' => $result->getDetail()]);
                }
                else
                {
                    dispatch(new CheckPendingReceipt($userId, $receipt, $this->delivery));
                    if ($expectJson)
                    {
                        return ['error' => 0, 'message' => 'success', 'data' => $result->toArray()];
                    }
                    return view('hanoivip::webtopup-success');
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error("WebTopup payment callback exception:" . $ex->getMessage());
            dispatch(new CheckPendingReceipt($userId, $receipt, $this->delivery))->delay(60);
            if ($expectJson)
            {
                return ['error' => 0, 'message' => 'We are trying our best to finish your payment', 'data' => $result->toArray()];
            }
            return view('hanoivip::webtopup-failure', ['message' => 'We are trying our best to finish your payment.']);
        }
    }
}