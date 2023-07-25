<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Services\DashboardService;
use App\Http\Requests\UserRequests\DeviceTokenRequest;

class DashboardController extends Controller
{
    public function __construct(DashboardService $DashboardService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->dashboard_service = $DashboardService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function userData()
    {
        $data = array();
        $data['user_data'] = $this->dashboard_service->userData();

        if (!$data['user_data'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "No data available!", $data));

        return ($this->global_api_response->success(count($data), "User data!", $data['user_data']['record']));
    }

    public function userJobsDetails()
    {
        $data = array();
        $data['user_job_data'] = $this->dashboard_service->userData();

        if (!$data['user_job_data'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "No data available!", $data));

        return ($this->global_api_response->success(count($data), "User data!", $data['user_job_data']['record']));
    }

    public function acceptJob($id)
    {
        $update_data = array();
        $update_data['user_job_data'] = $this->dashboard_service->acceptJob($id);

        if (!$update_data['user_job_data'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "No data available!", $update_data['user_job_data']));

        if ($update_data['user_job_data']['outcomeCode'] === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "No data available!", null));

        return ($this->global_api_response->success(count($update_data), "Job status updated successfully!", $update_data['user_job_data']['record']));
    }

    public function deviceToken(DeviceTokenRequest $request)
    {
        $device_token = $this->dashboard_service->deviceToken($request);
        // dd($device_token);
        if ($device_token == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "user not found!", []));

        if (!$device_token)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User device token not updated!", $device_token));

        return ($this->global_api_response->success(1, "User device token save successfully!", $device_token));
    }
}
