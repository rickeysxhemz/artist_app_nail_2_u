<?php

namespace App\Services;

use App\Helper\Helper;
use App\Libs\Response\GlobalApiResponseCodeBook;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService extends BaseService
{
    public function getDetails()
    {
        try {
            $bookings = Booking::with([
                'service:id,artist_id,name,price'
            ])
                ->where('artist_id', Auth::id())
                ->get(['id', 'service_id', 'artist_id', 'client_id', 'started_at', 'ended_at', 'status']);

            if ($bookings) {
                return $bookings;
            }
            return intval(GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode']);
        } catch (Exception $e) {

            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:PaymentService: getDetails", $error);
            return false;
        }
    }

    public function getTotalEarning()
    {
        try {
            
            $booking_service = Booking::whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('payments')
                        ->where('payments.artist_id', '=', Auth::id())
                        ->where('payments.status', 'pending')
                        ->whereRaw('bookings.id = payments.booking_id');
                })
                ->with('BookingService', 'Transaction')
                ->get();   
             
            $pending_list = [];

            foreach ($booking_service as $services) {

                    $data['services_name'] = $services['BookingService'];
                    $data['created_time'] = date("h:i a", strtotime($services['transaction']['created_at']));
                    $data['created_day'] = Helper::getDays(Carbon::parse($services['transaction']['created_at']));
                    $data['price'] = $services['total_price'];

                    array_push($pending_list, $data);
            }

            $complete_service = Booking::whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('payments')
                    ->where('payments.artist_id', '=', Auth::id())
                    ->where('payments.status', 'completed')
                    ->whereRaw('bookings.id = payments.booking_id');
            })
            ->with('BookingService', 'Transaction')
            ->get();   
            
            $completed_list = [];

            foreach ($complete_service as $services) {

                $data['services_name'] = $services['BookingService'];
                $data['created_time'] = date("h:i a", strtotime($services['transaction']['created_at']));
                $data['created_day'] = Helper::getDays(Carbon::parse($services['transaction']['created_at']));
                $data['price'] = $services['total_price'];

                array_push($completed_list, $data);
            }

            $users  = Payment::where('artist_id', Auth::id())
                            ->where('status', 'completed')
                            ->with('Client:id,username,address,cv_url,image_url')
                            ->select('id', 'amount', 'client_id')
                            ->orderBy('id', 'desc')
                            ->take(1)
                            ->get();
            
            $user_list = [];
            
            foreach ($users as $user) {
                $datas['username'] = $user->Client->username;
                $datas['image'] = "https://user.nail2u.net/" .$user->Client->image_url;

                array_push($user_list, $datas);
            }            
            $data = [
                "total_earning" => Payment::where('artist_id', Auth::id())->where('status', 'completed')->sum('amount'),
                "available_balance" => Payment::where('artist_id', Auth::id())->where('status', 'withdraw')->sum('amount'),
                'user'=> $user_list,
                "pending" => $pending_list,
                "completed" => $completed_list,
            ];

            return Helper::returnRecord(GlobalApiResponseCodeBook::SUCCESS['outcomeCode'], $data);

        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            dd($error);
            Helper::errorLogs("Artist:PaymentService: getTotalEarning", $error);
            return false;
        }
    }
}
