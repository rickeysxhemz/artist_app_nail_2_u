<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\SchedulerBooking;
use App\Models\BookingLocation;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\CommonTrait;

class BookingService extends BaseService
{
    use CommonTrait;
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
    public function cancelBooking($id)
    {
        try {
            DB::beginTransaction();
            $booking = Booking::where('id', $id)->first();
            $booking->status = 'cancel';
            $booking->save();

            $scheduler_booking = SchedulerBooking::where('booking_id', $id)->first();
            $scheduler_booking->status = 'cancel';
            $scheduler_booking->save();

            
            $booking_location = BookingLocation::where('booking_id', $id)->first();
            $booking_location->status = 'standby';
            $booking_location->save();

            $transaction=Transaction::where('booking_id', $id)->first();
            $transaction->transaction_status = 2;
            $transaction->save();
            DB::commit();

            $user="user";
            $bookingCancel="Booking Cancel !";
            $body="Booking is cancelled by the artist";
            $booking_cancel="1";
            $this->notifications($booking->client_id, $bookingCancel, $body, $booking_cancel, $user);

            return Helper::returnRecord(GlobalApiResponseCodeBook::RECORD_CREATED['outcomeCode'], ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            DB::rollBack();
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("BookingService: Cancel Booking", $error);
            return Helper::returnRecord(false, []);
        }
    }
}
