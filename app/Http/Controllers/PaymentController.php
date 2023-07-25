<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(PaymentService $PaymentService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->payment_service = $PaymentService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function getDetails()
    {
        $payment_details = $this->payment_service->getDetails();

        if ($payment_details === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Payment record not found!", $payment_details));

        if (!$payment_details)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Payment details did not fetched!", $payment_details));

        return ($this->global_api_response->success(count($payment_details), "Payment details fetched successfully!", $payment_details));
    }

    public function getTotalEarning(Request $request)
    {
        $total_earning = $this->payment_service->getTotalEarning($request);

        if (!$total_earning)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Total earning did not fetched!", $total_earning));

        return ($this->global_api_response->success(1, "Total earning fetched successfully!", $total_earning));
    }
}
