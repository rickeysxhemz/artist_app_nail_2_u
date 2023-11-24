<?php

namespace App\Http\Controllers;

use App\Http\Requests\Services\AddServicesRequest;
use App\Http\Requests\Services\EditServicesRequest;
use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Services\ServicesService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(ServicesService $AuthService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->services_service = $AuthService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function allRaw(Request $request)
    {
        $services_all = $this->services_service->allRaw($request);

        if (!$services_all)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not displayed!", $services_all));

        return ($this->global_api_response->success(count($services_all['record']), "Services displayed successfully!", $services_all['record']));

    }
    
    public function all(Request $request)
    {
        $services_all = $this->services_service->all($request);

        if (!$services_all)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not displayed!", $services_all));

        return ($this->global_api_response->success(1, "Services displayed successfully!", $services_all['record']));

    }

    public function add(AddServicesRequest $request)
    {
        $services = $this->services_service->add($request);

        if (!$services)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not created!", $services));

        return ($this->global_api_response->success(1, "Services created successfully!", $services['record']));

    }

    public function edit(EditServicesRequest $request)
    {
        $edit_services = $this->services_service->edit($request->all());

        if (!$edit_services)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not updated!", $edit_services));

        if ($edit_services['outcomeCode'] === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Record not exist!", null));

        return ($this->global_api_response->success(1, "Services updated successfully!", $edit_services['record']));

    }
    
    public function delete(Request $request)
    {
        $delete_services = $this->services_service->delete($request->all());

        if (!$delete_services)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not delete!", $delete_services));

        if ($delete_services['outcomeCode'] === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Record not exist!", null));

        return ($this->global_api_response->success(1, "Services delete successfully!", $delete_services['record']));

    }

    public function removeDiscount($id)
    {
        $edit_services = $this->services_service->removeDiscount($id);

        if (!$edit_services)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Services did not created!", $edit_services));

        if ($edit_services['outcomeCode'] === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Record not exist!", null));

        return ($this->global_api_response->success(1, "Services created successfully!", $edit_services['record']));

    }
}
