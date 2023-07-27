<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Http\Requests\Accounts\AccountLinkRequest;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(AccountService $AccountService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->account_service = $AccountService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function accountLink(AccountLinkRequest $request)
    {
        $account_link = $this->account_service->accountLink($request);

        if (!$account_link)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "account did not link!", $account_link));

        return ($this->global_api_response->success(1, "account linked successfully!", []));
    }
}
