<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\EditProfileRequest;
use App\Http\Requests\UserRequests\SaveAddressRequest;
use App\Http\Requests\UserRequests\UploadCoverImageRequest;
use App\Http\Requests\UserRequests\LocationStartRequest;
use App\Http\Requests\UserRequests\AdditionalInfoRequest;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Libs\Response\GlobalApiResponse;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(UserService $UserService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->user_service = $UserService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function getProfileDetails()
    {
        $profile_details = $this->user_service->getProfileDetails();

        if (!$profile_details)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User profile details did not fetched!", $profile_details));

        return ($this->global_api_response->success(1, "User profile details fetched successfully!", $profile_details));
    }

    public function editProfile(EditProfileRequest $request)
    {
        $edit_profile = $this->user_service->editProfile($request);

        if (!$edit_profile)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User profile did not edited!", $edit_profile));

        return ($this->global_api_response->success(1, "User profile edited successfully!", $edit_profile));
    }

    public function saveAddress(SaveAddressRequest $request)
    {
        $save_address = $this->user_service->saveAddress($request);

        if (!$save_address)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User address did not saved!", $save_address));

        return ($this->global_api_response->success(1, "User address saved successfully!", $save_address));
    }

    public function getAddresses()
    {
        $get_addresses = $this->user_service->getAddresses();

        if ($get_addresses == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "User addresses not found!", []));

        if (!$get_addresses)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User addresses did not fetched!", $get_addresses));

        return ($this->global_api_response->success(count($get_addresses), "User addresses fetched successfully!", $get_addresses));
    }

    public function uploadCoverImage(UploadCoverImageRequest $request)
    {
        $upload_image = $this->user_service->uploadCoverImage($request);
        
        if (!$upload_image)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Portfolio image did not uploaded!", $upload_image));

        return ($this->global_api_response->success(1, "Portfolio imaget uploaded successfully!", $upload_image));
    }

    public function getCoverImages()
    {
        $cover_images = $this->user_service->getCoverImages();
        if ($cover_images == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "cover image not found!", []));

        if (!$cover_images)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User cover images did not fetched!", $cover_images));

        return ($this->global_api_response->success(1, "User cover images fetched successfully!", $cover_images));
    }

    public function delete()
    {
        $deleted = $this->user_service->delete();
        if (!$deleted)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User did not deleted!", $deleted));
        return ($this->global_api_response->success(1, "User deleted successfully!", $deleted));
    }

    public function locationStart(LocationStartRequest $request)
    {
        $location_start = $this->user_service->locationStart($request);
        // dd($location_start);
        if ($location_start == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "booking id not found!", []));

        if (!$location_start)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User location not started!", $location_start));

        return ($this->global_api_response->success(1, "User location start successfully!", $location_start));
    }
    

    public function locationReached($id)
    {
        $location_reached = $this->user_service->locationReached($id);
        // dd($location_reached);
        if ($location_reached == GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "booking id not found!", []));

        if (!$location_reached)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "User location not reached!", $location_reached));

        return ($this->global_api_response->success(1, "User location reached successfully!", $location_reached));
    }
    
    public function additionalInfo(AdditionalInfoRequest $request)
    {
        $addition_info = $this->user_service->additionalInfo($request);

        if (!$addition_info)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "addition info not added!", $addition_info));

        return ($this->global_api_response->success(1, "addition info save successfully!", $addition_info));
    }

}
