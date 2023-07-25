<?php

namespace App\Http\Controllers;

use App\Http\Requests\Portfolio\DeleteImageRequest;
use App\Http\Requests\Portfolio\UploadImageRequest;
use App\Http\Requests\Portfolio\EditPortfolioRequest;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Libs\Response\GlobalApiResponse;
use App\Services\PortfolioService;

class PortfolioController extends Controller
{
    public function __construct(PortfolioService $PortfolioService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->portfolio_service = $PortfolioService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function getDetails()
    {
        $portfolio_details = $this->portfolio_service->getDetails();

        if (!$portfolio_details)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Portfolio details did not fetched!", $portfolio_details));

        return ($this->global_api_response->success(1, "Portfolio details fetched successfully!", $portfolio_details));
    }

    public function getImages()
    {
        $portfolio_images = $this->portfolio_service->getImages();
        if ($portfolio_images == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Portfolio image not found!", []));

        if (!$portfolio_images)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User portfolio images did not fetched!", $portfolio_images));

        return ($this->global_api_response->success(1, "User portfolio images fetched successfully!", $portfolio_images));
    }

    public function uploadImage(UploadImageRequest $request)
    {
        $upload_image = $this->portfolio_service->uploadImage($request);
        
        if (!$upload_image)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Portfolio image did not uploaded!", $upload_image));

        return ($this->global_api_response->success(1, "Portfolio imaget uploaded successfully!", $upload_image));
    }
    
    public function edit(EditPortfolioRequest $request)
    {
        $edit = $this->portfolio_service->edit($request);
        
        if (!$edit)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Portfolio image did not update!", $edit));

        if ($edit['outcomeCode'] == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Portfolio not found!", []));
        return ($this->global_api_response->success(1, "Portfolio update successfully!", $edit));
    }

    public function deleteImage(DeleteImageRequest $request)
    {
        $delete_image = $this->portfolio_service->deleteImage($request);

        if (!$delete_image)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Portfolio image did not deleted!", $delete_image));

        return ($this->global_api_response->success(1, "Portfolio imaget deleted successfully!", $delete_image));
    }
}
