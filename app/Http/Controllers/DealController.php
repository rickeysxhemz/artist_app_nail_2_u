<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Services\DealService;

class DealController extends Controller
{
    public function __construct(DealService $deal_service, GlobalApiResponse $GlobalApiResponse)
    {
        $this->deal_service = $deal_service;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function all()
    {
        $data = $this->deal_service->all();

        if (!$data)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "No data available!", $data));

        return ($this->global_api_response->success(count($data['record']), "Deals data!", $data['record']));
    }
    
    public function dealJoin($id)
    {
        $data = $this->deal_service->dealJoin($id);

        if (!$data)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Deal not join!", $data));

        return ($this->global_api_response->success(1, "Deals Join successfully!", $data['record']));
    }
}