<?php

namespace App\Http\Controllers;

use App\Libs\Response\GlobalApiResponse;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Http\Requests\Bookings\GetJobHistoryRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(BookingService $BookingService, GlobalApiResponse $GlobalApiResponse)
    {
        $this->booking_service = $BookingService;
        $this->global_api_response = $GlobalApiResponse;
    }

    public function getJobHistory(GetJobHistoryRequest $request)
    {
        $job_history = $this->booking_service->getJobHistory($request);
        
        if ($job_history === GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS, "Job history details not found!", []));

        if (!$job_history)
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Job history details did not fetched!", $job_history));

        return ($this->global_api_response->success(count($job_history), "Job history details fetched successfully!", $job_history));
    }
    public function cancelBooking($id)
    {
        $cancel_booking = $this->booking_service->cancelBooking($id);

        if (!$cancel_booking['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Booking cancel failed!", $cancel_booking['record']));

        return ($this->global_api_response->success(count($cancel_booking), "Booking cancel successfully!", $cancel_booking['record']));
    }
    public function setUnavailable(Request $request)
    {
        $set_unavailable = $this->booking_service->setUnavailable($request);

        if (!$set_unavailable['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Booking unavailable setting failed!", $set_unavailable['record']));

        return ($this->global_api_response->success(count($set_unavailable), "Booking unavailable time set successfully!", $set_unavailable['record']));
    }
    public function listSchedular()
    {
        $list_schedular = $this->booking_service->listSchedular();

        if (!$list_schedular['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Schedular list failed!", $list_schedular['record']));

        return ($this->global_api_response->success(count($list_schedular), "Schedular list successfully!", $list_schedular['record']));
    }
    public function setAvailable(Request $request)
    {
        $set_available = $this->booking_service->setAvailable($request);

        if (!$set_available['outcomeCode'])
            return ($this->global_api_response->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, "Booking available time setting failed!", $set_available['record']));

        return ($this->global_api_response->success(count($set_available), "Booking available time set successfully!", $set_available['record']));
    }
}
