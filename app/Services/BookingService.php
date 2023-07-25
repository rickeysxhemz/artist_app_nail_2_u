<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Libs\Response\GlobalApiResponseCodeBook;

class BookingService extends BaseService
{
   public function getJobHistory($request)
    {
        try {
            $response = [];
            $value = Auth::id();
            $bookings = Booking::with([
                'Client:id,username,address,cv_url,image_url',
                'BookingService:id,name,amount',
                'ScheduleBooking:id,time'
            ])
            ->where('status', $request->status)
            ->where('artist_id', $value)
            ->select('id', 'total_price', 'client_id')
            ->orderBy('id', 'desc')
            ->get();
            
            if ($bookings->isNotEmpty()) {
                // foreach ($bookings as $booking) {
                //     $temp['client_name'] = $booking->client->username;
                //     $temp['address'] = $booking->client->address;
                //     $temp['amount'] = $booking->total_price;
                //     $temp['time'] = date('h:i a', strtotime($booking->Transaction->created_at));
                //     $temp['day'] = Helper::getDays($booking->Transaction->created_at);
                //     $temp['service'] = $booking->BookingService;
                //     array_push($response, $temp);
                // }
                return $bookings;
            }
            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:BookingService: getJobHistory", $error);
            return false;
        }
    }
}
