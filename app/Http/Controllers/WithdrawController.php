<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Services\WithdrawService;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function __construct(WithdrawService $WithdrawService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->withdraw_service = $WithdrawService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function withdrawPayment()
    {
        $withdraw = $this->withdraw_service->withdrawPayment();

        if ($withdraw['outcomeCode'] == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "record not found!", []));
        if (!$withdraw)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "payment did not withdraw!", $withdraw));

        return ($this->global_api_response->success(1, "payment withdraw successfully!", []));
    }
}
